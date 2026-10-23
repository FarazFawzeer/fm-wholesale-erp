<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Cheque;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    /**
     * Record a receipt (in, from customer) or payment (out, to supplier).
     *
     * $lines:       [['method' => 'cash|bank_transfer|card|cheque', 'amount' => .., 'account_id' => ..,
     *                 'reference' => .., 'cheque_no' => .., 'bank' => .., 'cheque_date' => ..], ...]
     * $allocations: [invoice_id|purchase_order_id => amount]
     */
    public static function record(string $direction, string $partyType, int $partyId, string $date,
        array $lines, array $allocations = [], ?string $notes = null): Payment
    {
        $lines = array_values(array_filter($lines, fn ($l) => (float) ($l['amount'] ?? 0) > 0));
        if (! $lines) {
            throw ValidationException::withMessages(['lines' => 'Enter at least one payment amount.']);
        }
        $total = round(array_sum(array_map(fn ($l) => (float) $l['amount'], $lines)), 2);
        $allocations = array_filter(array_map('floatval', $allocations), fn ($a) => $a > 0);
        if (array_sum($allocations) - $total > 0.009) {
            throw ValidationException::withMessages(['allocations' => 'Allocated amount is more than the payment total.']);
        }

        return DB::transaction(function () use ($direction, $partyType, $partyId, $date, $lines, $allocations, $notes, $total) {
            $payment = Payment::create([
                'number' => NumberService::next($direction === 'in' ? 'RCT' : 'PAY'),
                'direction' => $direction,
                'party_type' => $partyType,
                'party_id' => $partyId,
                'date' => $date,
                'total' => $total,
                'notes' => $notes,
                'user_id' => auth()->id(),
            ]);
            $sign = $direction === 'in' ? 1 : -1;
            $partyName = $payment->party_name;

            foreach ($lines as $line) {
                $chequeId = null;
                $accountId = $line['account_id'] ?? null;

                if ($line['method'] === 'cheque') {
                    if (empty($line['cheque_no']) || empty($line['cheque_date'])) {
                        throw ValidationException::withMessages(['lines' => 'Cheque number and cheque date are required.']);
                    }
                    $chequeId = Cheque::create([
                        'direction' => $direction === 'in' ? 'received' : 'issued',
                        'cheque_no' => $line['cheque_no'],
                        'bank' => $line['bank'] ?? null,
                        'cheque_date' => $line['cheque_date'],
                        'amount' => $line['amount'],
                        'party_type' => $partyType,
                        'party_id' => $partyId,
                        'payment_id' => $payment->id,
                        'account_id' => $accountId,
                        'status' => 'pending',
                    ])->id;
                } else {
                    $accountId = $accountId ?: static::defaultAccountFor($line['method']);
                    LedgerService::post($accountId, $date, $direction === 'in' ? 'receipt' : 'payment',
                        ($direction === 'in' ? 'Receipt from ' : 'Payment to ') . $partyName . ' (' . $payment->number . ')',
                        $sign * (float) $line['amount'], $payment);
                }

                $payment->lines()->create([
                    'method' => $line['method'],
                    'account_id' => $accountId,
                    'cheque_id' => $chequeId,
                    'amount' => $line['amount'],
                    'reference' => $line['reference'] ?? null,
                ]);
            }

            foreach ($allocations as $docId => $amount) {
                if ($partyType === 'customer') {
                    $invoice = Invoice::where('customer_id', $partyId)->findOrFail($docId);
                    $payment->allocations()->create(['invoice_id' => $invoice->id, 'amount' => min($amount, $invoice->balance())]);
                    $invoice->refreshPaymentStatus();
                } else {
                    $po = PurchaseOrder::where('supplier_id', $partyId)->findOrFail($docId);
                    $payment->allocations()->create(['purchase_order_id' => $po->id, 'amount' => $amount]);
                    $po->update(['paid_amount' => $po->paid_amount + $amount]);
                }
            }

            return $payment;
        });
    }

    /** Auto-allocate a receipt to the oldest open invoices of a customer. */
    public static function oldestFirst(int $customerId, float $amount): array
    {
        $alloc = [];
        foreach (Invoice::where('customer_id', $customerId)->open()->orderBy('due_date')->get() as $invoice) {
            if ($amount <= 0) {
                break;
            }
            $take = min($amount, $invoice->balance());
            $alloc[$invoice->id] = $take;
            $amount -= $take;
        }

        return $alloc;
    }

    public static function defaultAccountFor(string $method): int
    {
        $type = match ($method) {
            'cash' => 'cash',
            'card' => 'card',
            default => 'bank',
        };

        return Account::where('type', $type)->orderBy('id')->value('id') ?? Account::orderBy('id')->value('id');
    }

    /** Move a cheque through deposited -> cleared / bounced, posting to the bank when money moves. */
    public static function updateChequeStatus(Cheque $cheque, string $status, string $date, ?int $accountId = null, ?string $remarks = null): void
    {
        DB::transaction(function () use ($cheque, $status, $date, $accountId, $remarks) {
            $accountId = $accountId ?: $cheque->account_id ?: static::defaultAccountFor('bank_transfer');
            $received = $cheque->direction === 'received';
            $partyName = $cheque->party()?->name;

            if ($status === 'cleared' && $cheque->status !== 'cleared') {
                LedgerService::post($accountId, $date, $received ? 'cheque_cleared' : 'cheque_paid',
                    'Cheque ' . $cheque->cheque_no . ($received ? ' from ' : ' to ') . $partyName,
                    $received ? $cheque->amount : -$cheque->amount, $cheque);
            }

            if (in_array($status, ['bounced', 'cancelled']) && $cheque->isOpen()) {
                static::reverseChequeFromPayment($cheque);
            }

            $cheque->update([
                'status' => $status,
                'status_date' => $date,
                'account_id' => $accountId,
                'remarks' => $remarks ?? $cheque->remarks,
            ]);
        });
    }

    /** A bounced cheque means the payment never happened: reduce the payment and re-open invoices / POs. */
    protected static function reverseChequeFromPayment(Cheque $cheque): void
    {
        $payment = $cheque->payment;
        if (! $payment || $payment->total <= 0) {
            return;
        }
        $ratio = min(1, $cheque->amount / $payment->total);

        foreach ($payment->allocations as $allocation) {
            $reduce = round($allocation->amount * $ratio, 2);
            $allocation->update(['amount' => $allocation->amount - $reduce]);
            if ($allocation->invoice) {
                $allocation->invoice->refreshPaymentStatus();
            }
            if ($allocation->purchaseOrder) {
                $allocation->purchaseOrder->decrement('paid_amount', $reduce);
            }
        }
        $payment->update([
            'total' => $payment->total - $cheque->amount,
            'notes' => trim($payment->notes . "\nCheque " . $cheque->cheque_no . ' returned (' . money($cheque->amount) . ').'),
        ]);
    }
}
