<?php

namespace App\Services;

use App\Models\Cheque;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    /** Keep only filled payment lines: [['method' => cash|card|online|cheque, 'amount' => .., ...], ...] */
    public static function cleanLines(array $lines): array
    {
        $lines = array_values(array_filter($lines, fn ($l) => round((float) ($l['amount'] ?? 0), 2) > 0));
        foreach ($lines as &$l) {
            $l['amount'] = round((float) $l['amount'], 2);
            if (! array_key_exists($l['method'] ?? '', Payment::METHODS)) {
                $l['method'] = 'cash';
            }
            if ($l['method'] === 'cheque' && empty($l['cheque_no'])) {
                throw ValidationException::withMessages(['payments' => 'Enter the cheque number.']);
            }
        }

        return $lines;
    }

    public static function linesTotal(array $lines): float
    {
        return round(array_sum(array_map(fn ($l) => (float) $l['amount'], $lines)), 2);
    }

    /**
     * Record money in (from a customer) or out (to a supplier).
     *
     * $allocations: [['invoice_id' => 5, 'amount' => 1000], ['purchase_id' => 2, ...], ['amount' => 500] (old balance)]
     */
    public static function record(string $direction, string $partyType, int $partyId, string $date,
        array $lines, array $allocations, ?string $notes = null): ?Payment
    {
        $lines = static::cleanLines($lines);
        if (! $lines) {
            return null;
        }
        $total = static::linesTotal($lines);

        return DB::transaction(function () use ($direction, $partyType, $partyId, $date, $lines, $allocations, $notes, $total) {
            $payment = Payment::create([
                'number' => NumberService::next($direction === 'in' ? 'RC' : 'PY'),
                'direction' => $direction,
                'party_type' => $partyType,
                'party_id' => $partyId,
                'date' => $date,
                'total' => $total,
                'notes' => $notes,
                'user_id' => auth()->id(),
            ]);

            foreach ($lines as $line) {
                $chequeId = null;
                if ($line['method'] === 'cheque') {
                    $chequeId = Cheque::create([
                        'direction' => $direction === 'in' ? 'received' : 'issued',
                        'cheque_no' => $line['cheque_no'],
                        'bank' => $line['bank'] ?? null,
                        'cheque_date' => ($line['cheque_date'] ?? null) ?: $date,
                        'amount' => $line['amount'],
                        'party_type' => $partyType,
                        'party_id' => $partyId,
                        'payment_id' => $payment->id,
                    ])->id;
                }
                $payment->lines()->create([
                    'method' => $line['method'],
                    'amount' => $line['amount'],
                    'reference' => $line['reference'] ?? null,
                    'cheque_id' => $chequeId,
                ]);
            }

            foreach ($allocations as $a) {
                if (round((float) $a['amount'], 2) <= 0) {
                    continue;
                }
                $alloc = $payment->allocations()->create([
                    'invoice_id' => $a['invoice_id'] ?? null,
                    'purchase_id' => $a['purchase_id'] ?? null,
                    'amount' => round((float) $a['amount'], 2),
                ]);
                $alloc->invoice?->refreshPaid();
                $alloc->purchase?->refreshPaid();
            }

            return $payment;
        });
    }

    /**
     * Spread an amount over what a customer / supplier owes: old balance first, then the oldest bills.
     * Stops if the amount is more than they owe.
     */
    public static function oldestFirst(string $partyType, int $partyId, float $amount, ?int $onlyDocId = null): array
    {
        $party = $partyType === 'customer' ? Customer::findOrFail($partyId) : Supplier::findOrFail($partyId);
        $docs = $partyType === 'customer'
            ? Invoice::where('customer_id', $partyId)->unpaid()->orderBy('date')->orderBy('id')->get()
            : Purchase::where('supplier_id', $partyId)->unpaid()->orderBy('date')->orderBy('id')->get();
        $key = $partyType === 'customer' ? 'invoice_id' : 'purchase_id';

        if ($onlyDocId) { // pay one bill first, then anything left goes to the others
            $docs = $docs->sortBy(fn ($d) => $d->id == $onlyDocId ? 0 : 1)->values();
        }

        $alloc = [];
        $left = round($amount, 2);
        $opening = $party->openingDue();
        if ($opening > 0 && ! $onlyDocId) {
            $take = min($left, $opening);
            $alloc[] = ['amount' => $take];
            $left = round($left - $take, 2);
        }
        foreach ($docs as $doc) {
            if ($left <= 0) {
                break;
            }
            $take = min($left, $doc->balance());
            $alloc[] = [$key => $doc->id, 'amount' => $take];
            $left = round($left - $take, 2);
        }
        if ($left > 0 && $onlyDocId && $opening > 0) {
            $take = min($left, $opening);
            $alloc[] = ['amount' => $take];
            $left = round($left - $take, 2);
        }
        if ($left > 0.009) {
            throw ValidationException::withMessages(['payments' => 'The amount is ' . money($left) . ' more than what is owed (' . money($amount - $left) . ').']);
        }

        return $alloc;
    }

    /**
     * After goods come back a bill can end up paid more than its new total.
     * Take that extra off the bill (newest payment first) so it can be used elsewhere.
     */
    public static function freeExcess(Invoice|Purchase $doc): void
    {
        $doc->refresh();
        $excess = round($doc->paid_amount - ($doc->total - $doc->returned_amount), 2);
        if ($excess <= 0.009) {
            return;
        }
        foreach ($doc->allocations()->orderByDesc('id')->get() as $allocation) {
            if ($excess <= 0) {
                break;
            }
            $reduce = min($excess, (float) $allocation->amount);
            $left = round($allocation->amount - $reduce, 2);
            $left > 0 ? $allocation->update(['amount' => $left]) : $allocation->delete();
            $excess = round($excess - $reduce, 2);
        }
        $doc->refreshPaid();
    }

    /** Money paid that is not on any bill yet (credit). */
    public static function unusedCredit(string $partyType, int $partyId): float
    {
        $paid = (float) Payment::where('party_type', $partyType)->where('party_id', $partyId)->sum('total');
        $used = (float) PaymentAllocation::whereHas('payment', fn ($q) => $q->where('party_type', $partyType)->where('party_id', $partyId))->sum('amount');

        return max(0, round($paid - $used, 2));
    }

    /**
     * Put any unused money (credit) on the old balance first, then the oldest unpaid bills.
     * Whatever is left stays as credit and is used on the next bill automatically.
     */
    public static function applyCredit(string $partyType, int $partyId): void
    {
        if (static::unusedCredit($partyType, $partyId) <= 0.009) {
            return;
        }
        $party = $partyType === 'customer' ? Customer::findOrFail($partyId) : Supplier::findOrFail($partyId);
        $key = $partyType === 'customer' ? 'invoice_id' : 'purchase_id';
        $docs = $partyType === 'customer'
            ? Invoice::where('customer_id', $partyId)->unpaid()->orderBy('date')->orderBy('id')->get()
            : Purchase::where('supplier_id', $partyId)->unpaid()->orderBy('date')->orderBy('id')->get();

        $payments = Payment::where('party_type', $partyType)->where('party_id', $partyId)
            ->withSum('allocations', 'amount')->orderBy('date')->orderBy('id')->get()
            ->map(fn ($p) => ['id' => $p->id, 'free' => round($p->total - (float) $p->allocations_sum_amount, 2)])
            ->filter(fn ($p) => $p['free'] > 0.009)->values()->all();

        $targets = [];
        if (($opening = $party->openingDue()) > 0.009) {
            $targets[] = [null, $opening];
        }
        foreach ($docs as $doc) {
            $targets[] = [$doc, $doc->balance()];
        }

        foreach ($targets as [$doc, $need]) {
            foreach ($payments as &$p) {
                if ($need <= 0.009) {
                    break;
                }
                if ($p['free'] <= 0.009) {
                    continue;
                }
                $take = round(min($need, $p['free']), 2);
                PaymentAllocation::create(['payment_id' => $p['id'], $key => $doc?->id, 'amount' => $take]);
                $p['free'] = round($p['free'] - $take, 2);
                $need = round($need - $take, 2);
            }
            unset($p);
            $doc?->refreshPaid();
        }
    }

    /** Cheque cleared, or returned (bounced). A bounced cheque cancels that part of the payment. */
    public static function updateCheque(Cheque $cheque, string $status, string $date): void
    {
        DB::transaction(function () use ($cheque, $status, $date) {
            if ($status === 'bounced' && $cheque->status !== 'bounced') {
                static::reverseCheque($cheque);
            }
            $cheque->update(['status' => $status, 'status_date' => $date]);
        });
    }

    protected static function reverseCheque(Cheque $cheque): void
    {
        $payment = $cheque->payment;
        if (! $payment || $payment->total <= 0) {
            return;
        }
        $left = min($cheque->amount, $payment->total);
        // take the money back from the newest bills first
        foreach ($payment->allocations()->orderByDesc('id')->get() as $allocation) {
            if ($left <= 0) {
                break;
            }
            $reduce = min($left, $allocation->amount);
            $allocation->update(['amount' => round($allocation->amount - $reduce, 2)]);
            $left = round($left - $reduce, 2);
            $allocation->invoice?->refreshPaid();
            $allocation->purchase?->refreshPaid();
        }
        $payment->update([
            'total' => round($payment->total - $cheque->amount, 2),
            'notes' => trim($payment->notes . "\nCheque " . $cheque->cheque_no . ' returned (' . money($cheque->amount) . ').'),
        ]);
    }
}
