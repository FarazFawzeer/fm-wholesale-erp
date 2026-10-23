<?php

namespace App\Services;

use App\Models\CreditNote;
use App\Models\Customer;
use App\Models\DeliveryNote;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SalesOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesService
{
    /**
     * Expand size-grid lines into one sales line per size and enforce pricing rules.
     * Returns [rows, subtotal, discount, total, cost].
     */
    public static function buildLines(array $lines, bool $enforceRules = true): array
    {
        $rows = [];
        $gross = 0;
        $net = 0;
        $cost = 0;
        $maxDiscount = (float) setting('max_discount_percent');
        $isManager = auth()->user()?->isManager() ?? true;
        $errors = [];

        foreach ($lines as $line) {
            $product = Product::find($line['product_id'] ?? 0);
            if (! $product) {
                continue;
            }
            $price = (float) ($line['unit_price'] ?? 0);
            $discount = (float) ($line['discount_percent'] ?? 0);
            $netPrice = $price * (1 - $discount / 100);

            if ($enforceRules && ! $isManager) {
                if ($discount > $maxDiscount) {
                    $errors[] = "{$product->name}: discount {$discount}% is above the allowed {$maxDiscount}%.";
                }
                if ($netPrice < $product->minimumPrice()) {
                    $errors[] = "{$product->name}: price " . money($netPrice) . ' is below the minimum selling price ' . money($product->minimumPrice()) . '.';
                }
            }

            foreach (($line['qty'] ?? []) as $variantId => $qty) {
                $qty = (int) $qty;
                if ($qty <= 0) {
                    continue;
                }
                $variant = ProductVariant::where('product_id', $product->id)->findOrFail($variantId);
                $lineTotal = round($qty * $netPrice, 2);
                $rows[] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'qty' => $qty,
                    'unit_price' => $price,
                    'discount_percent' => $discount,
                    'line_total' => $lineTotal,
                    'unit_cost' => $product->cost_price,
                ];
                $gross += $qty * $price;
                $net += $lineTotal;
                $cost += $qty * $product->cost_price;
            }
        }

        if ($errors) {
            throw ValidationException::withMessages(['lines' => $errors]);
        }
        if (! $rows) {
            throw ValidationException::withMessages(['lines' => 'Add at least one product with a quantity.']);
        }

        return [$rows, round($gross, 2), round($gross - $net, 2), round($net, 2), round($cost, 2)];
    }

    public static function saveSalesOrder(?SalesOrder $doc, array $data, array $lines): SalesOrder
    {
        [$rows, $gross, $discount, $total] = static::buildLines($lines);

        return DB::transaction(function () use ($doc, $data, $rows, $gross, $discount, $total) {
            $doc ??= new SalesOrder([
                'number' => NumberService::next($data['type'] === 'quotation' ? 'QT' : 'SO'),
                'user_id' => auth()->id(),
                'status' => 'open',
            ]);
            $doc->fill([
                'type' => $data['type'],
                'customer_id' => $data['customer_id'],
                'date' => $data['date'],
                'valid_until' => $data['valid_until'] ?? null,
                'notes' => $data['notes'] ?? null,
                'subtotal' => $gross,
                'discount_amount' => $discount,
                'total' => $total,
            ])->save();
            $doc->lines()->delete();
            $doc->lines()->createMany($rows);

            return $doc;
        });
    }

    /** Credit check: block credit sales that push a customer over the limit or while invoices are overdue. */
    public static function checkCredit(Customer $customer, float $newAmount, bool $override = false): ?string
    {
        if ($override && auth()->user()?->isManager()) {
            return null;
        }
        if ($customer->credit_limit > 0 && $customer->balance() + $newAmount > $customer->credit_limit) {
            return "Credit limit exceeded for {$customer->name}. Limit " . money($customer->credit_limit)
                . ', current balance ' . money($customer->balance()) . ', this invoice ' . money($newAmount) . '.';
        }
        if ($customer->overdueAmount() > 0) {
            return "{$customer->name} has overdue invoices (" . money($customer->overdueAmount()) . '). Collect payment or ask a manager to override.';
        }

        return null;
    }

    public static function createInvoice(array $data, array $lines, bool $deliverNow, array $paymentLines = []): Invoice
    {
        [$rows, $gross, $discount, $total, $cost] = static::buildLines($lines);
        $customer = Customer::findOrFail($data['customer_id']);

        if ($data['sale_type'] === 'credit') {
            $paidNow = array_sum(array_map(fn ($l) => (float) ($l['amount'] ?? 0), $paymentLines));
            if ($problem = static::checkCredit($customer, $total - $paidNow, ! empty($data['override_credit']))) {
                throw ValidationException::withMessages(['customer_id' => $problem]);
            }
        }

        return DB::transaction(function () use ($data, $rows, $gross, $discount, $total, $cost, $customer, $deliverNow, $paymentLines) {
            $invoice = Invoice::create([
                'number' => NumberService::next('INV'),
                'customer_id' => $customer->id,
                'sales_order_id' => $data['sales_order_id'] ?? null,
                'warehouse_id' => $data['warehouse_id'],
                'date' => $data['date'],
                'due_date' => $data['sale_type'] === 'cash' ? $data['date'] : now()->parse($data['date'])->addDays($customer->payment_terms_days),
                'sale_type' => $data['sale_type'],
                'subtotal' => $gross,
                'discount_amount' => $discount,
                'total' => $total,
                'cost_total' => $cost,
                'notes' => $data['notes'] ?? null,
                'user_id' => auth()->id(),
            ]);
            $invoice->lines()->createMany($rows);

            if (! empty($data['sales_order_id'])) {
                SalesOrder::whereKey($data['sales_order_id'])->update(['status' => 'invoiced']);
            }

            if ($deliverNow) {
                $invoice->load('lines');
                static::deliver($invoice, $invoice->lines->pluck('qty', 'id')->all(), ['date' => $data['date']]);
            }

            if ($paymentLines) {
                PaymentService::record('in', 'customer', $customer->id, $data['date'], $paymentLines,
                    [$invoice->id => array_sum(array_map(fn ($l) => (float) ($l['amount'] ?? 0), $paymentLines))]);
            }

            if ($data['sale_type'] === 'cash' && $invoice->fresh()->balance() > 0.009) {
                throw ValidationException::withMessages(['payments' => 'Cash sales must be fully paid. Balance: ' . money($invoice->fresh()->balance())]);
            }

            return $invoice->fresh();
        });
    }

    /** Full or partial delivery. $qtys = [sales_line_id => qty]. */
    public static function deliver(Invoice $invoice, array $qtys, array $data): DeliveryNote
    {
        return DB::transaction(function () use ($invoice, $qtys, $data) {
            $note = DeliveryNote::create([
                'number' => NumberService::next('DN'),
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id,
                'warehouse_id' => $invoice->warehouse_id,
                'date' => $data['date'],
                'vehicle_no' => $data['vehicle_no'] ?? null,
                'received_by' => $data['received_by'] ?? null,
                'notes' => $data['notes'] ?? null,
                'user_id' => auth()->id(),
            ]);

            foreach ($invoice->lines as $line) {
                $qty = min((int) ($qtys[$line->id] ?? 0), $line->qty - $line->delivered_qty);
                if ($qty <= 0) {
                    continue;
                }
                StockService::move($line->product_variant_id, $invoice->warehouse_id, -$qty, 'sale', (float) $line->unit_cost,
                    $note, 'Delivered for ' . $invoice->number, $data['date']);
                $note->items()->create(['sales_line_id' => $line->id, 'product_variant_id' => $line->product_variant_id, 'qty' => $qty]);
                $line->increment('delivered_qty', $qty);
            }

            if (! $note->items()->exists()) {
                throw ValidationException::withMessages(['qty' => 'Enter a quantity to deliver.']);
            }
            $invoice->refreshDeliveryStatus();

            return $note;
        });
    }

    /** Sales return / credit note. $qtys = [sales_line_id => qty]. */
    public static function creditNote(Invoice $invoice, array $qtys, array $data): CreditNote
    {
        return DB::transaction(function () use ($invoice, $qtys, $data) {
            $note = CreditNote::create([
                'number' => NumberService::next('CN'),
                'invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id,
                'date' => $data['date'],
                'reason' => $data['reason'] ?? null,
                'restock' => ! empty($data['restock']),
                'user_id' => auth()->id(),
            ]);
            $total = 0;
            $costReturned = 0;

            foreach ($invoice->lines as $line) {
                $max = ($note->restock ? $line->delivered_qty : $line->qty) - $line->returned_qty;
                $qty = min((int) ($qtys[$line->id] ?? 0), $max);
                if ($qty <= 0) {
                    continue;
                }
                $unit = round($line->line_total / $line->qty, 2);
                $note->items()->create([
                    'sales_line_id' => $line->id,
                    'product_variant_id' => $line->product_variant_id,
                    'qty' => $qty,
                    'unit_price' => $unit,
                    'line_total' => round($unit * $qty, 2),
                ]);
                if ($note->restock) {
                    StockService::move($line->product_variant_id, $invoice->warehouse_id, $qty, 'sales_return', (float) $line->unit_cost,
                        $note, 'Returned on ' . $invoice->number, $data['date']);
                }
                $line->increment('returned_qty', $qty);
                $total += $unit * $qty;
                $costReturned += $line->unit_cost * $qty;
            }

            if ($total <= 0) {
                throw ValidationException::withMessages(['qty' => 'Enter a quantity to return.']);
            }
            $note->update(['total' => round($total, 2)]);
            $invoice->update(['cost_total' => max(0, $invoice->cost_total - $costReturned)]);
            $invoice->refreshPaymentStatus();

            return $note;
        });
    }

    /** Copy lines from a quotation/order into the size-grid format used by the forms. */
    public static function toGrid(Model $doc): array
    {
        return $doc->lines->groupBy('product_id')->map(function ($rows, $productId) {
            $first = $rows->first();

            return [
                'product_id' => (int) $productId,
                'unit_price' => (float) $first->unit_price,
                'discount_percent' => (float) $first->discount_percent,
                'qty' => $rows->pluck('qty', 'product_variant_id')->map(fn ($q) => (int) $q)->all(),
            ];
        })->values()->all();
    }
}
