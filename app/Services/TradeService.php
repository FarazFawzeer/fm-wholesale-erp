<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\GoodsReturn;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\ProductColour;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/** Buying, selling and returns: the stock and money effects of each. */
class TradeService
{
    /**
     * Save a purchase: pairs go into stock straight away, payment (optional) is recorded against it.
     *
     * $items: [['product_id' => 1, 'price' => 2500, 'colours' => [['id' => 3 | null, 'name' => 'Black', 'qty' => 20], ...]], ...]
     */
    public static function purchase(array $data, array $items, array $paymentLines): Purchase
    {
        $rows = [];
        $subtotal = 0;
        foreach ($items as $item) {
            $product = Product::find($item['product_id'] ?? 0);
            $price = round((float) ($item['price'] ?? 0), 2);
            if (! $product) {
                continue;
            }
            foreach (($item['colours'] ?? []) as $c) {
                $qty = (int) ($c['qty'] ?? 0);
                $name = trim((string) ($c['name'] ?? ''));
                if ($qty <= 0) {
                    continue;
                }
                if ($name === '' && empty($c['id'])) {
                    throw ValidationException::withMessages(['items' => "Enter the colour name for {$product->name}."]);
                }
                $rows[] = compact('product', 'price', 'qty', 'name') + ['colour_id' => $c['id'] ?? null];
                $subtotal += $qty * $price;
            }
        }
        if (! $rows) {
            throw ValidationException::withMessages(['items' => 'Add at least one product with a quantity.']);
        }

        $subtotal = round($subtotal, 2);
        $discount = round((float) ($data['discount'] ?? 0), 2);
        $other = round((float) ($data['other_costs'] ?? 0), 2);
        $total = round($subtotal - $discount + $other, 2);
        if ($total < 0) {
            throw ValidationException::withMessages(['discount' => 'The discount is more than the bill.']);
        }
        $factor = $subtotal > 0 ? $total / $subtotal : 1; // spreads discount and transport over every pair

        $paymentLines = PaymentService::cleanLines($paymentLines);
        $paid = PaymentService::linesTotal($paymentLines);
        if ($paid - $total > 0.009) {
            throw ValidationException::withMessages(['payments' => 'Payment (' . money($paid) . ') is more than the bill (' . money($total) . ').']);
        }

        return DB::transaction(function () use ($data, $rows, $subtotal, $discount, $other, $total, $factor, $paymentLines, $paid) {
            $purchase = Purchase::create([
                'number' => NumberService::next('PU'),
                'supplier_id' => $data['supplier_id'],
                'supplier_bill_no' => $data['supplier_bill_no'] ?? null,
                'date' => $data['date'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'other_costs' => $other,
                'total' => $total,
                'notes' => $data['notes'] ?? null,
                'user_id' => auth()->id(),
            ]);

            foreach ($rows as $r) {
                $colour = $r['colour_id']
                    ? ProductColour::where('product_id', $r['product']->id)->findOrFail($r['colour_id'])
                    : StockService::colour($r['product'], $r['name']);
                $unitCost = round($r['price'] * $factor, 2);
                $purchase->items()->create([
                    'product_id' => $r['product']->id,
                    'product_colour_id' => $colour->id,
                    'qty' => $r['qty'],
                    'unit_price' => $r['price'],
                    'line_total' => round($r['qty'] * $r['price'], 2),
                    'unit_cost' => $unitCost,
                ]);
                StockService::receive($colour->id, $r['qty'], $unitCost, $purchase, 'Bought from ' . $purchase->supplier->name, $data['date']);
            }

            if ($paid > 0) {
                PaymentService::record('out', 'supplier', $purchase->supplier_id, $data['date'], $paymentLines,
                    [['purchase_id' => $purchase->id, 'amount' => $paid]], 'Paid with ' . $purchase->number);
            }
            PaymentService::applyCredit('supplier', $purchase->supplier_id); // money we paid earlier and not used yet

            return $purchase->fresh();
        });
    }

    /**
     * Save a sale: pairs leave stock, payment (optional, any mix of methods) is recorded.
     * Money above this bill goes to the customer's older dues.
     *
     * $items: [['product_id' => 1, 'price' => 3500, 'colours' => [colour_id => qty, ...]], ...]
     */
    public static function invoice(array $data, array $items, array $paymentLines): Invoice
    {
        $rows = [];
        $subtotal = 0;
        $cost = 0;
        foreach ($items as $item) {
            $product = Product::find($item['product_id'] ?? 0);
            $price = round((float) ($item['price'] ?? 0), 2);
            if (! $product) {
                continue;
            }
            foreach (($item['colours'] ?? []) as $colourId => $qty) {
                $qty = (int) $qty;
                if ($qty <= 0) {
                    continue;
                }
                $colour = ProductColour::where('product_id', $product->id)->findOrFail($colourId);
                $rows[] = [
                    'product_id' => $product->id,
                    'product_colour_id' => $colour->id,
                    'qty' => $qty,
                    'unit_price' => $price,
                    'line_total' => round($qty * $price, 2),
                    'unit_cost' => (float) $product->cost_price,
                ];
                $subtotal += $qty * $price;
                $cost += $qty * (float) $product->cost_price;
            }
        }
        if (! $rows) {
            throw ValidationException::withMessages(['items' => 'Add at least one product with a quantity.']);
        }

        $subtotal = round($subtotal, 2);
        $discount = round((float) ($data['discount'] ?? 0), 2);
        $total = round($subtotal - $discount, 2);
        if ($total < 0) {
            throw ValidationException::withMessages(['discount' => 'The discount is more than the bill.']);
        }
        $customer = Customer::findOrFail($data['customer_id']);
        $paymentLines = PaymentService::cleanLines($paymentLines);
        $paid = PaymentService::linesTotal($paymentLines);

        return DB::transaction(function () use ($data, $rows, $subtotal, $discount, $total, $cost, $customer, $paymentLines, $paid) {
            $invoice = Invoice::create([
                'number' => NumberService::next('INV'),
                'customer_id' => $customer->id,
                'date' => $data['date'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'cost_total' => round($cost, 2),
                'share_token' => Str::random(32),
                'notes' => $data['notes'] ?? null,
                'user_id' => auth()->id(),
            ]);

            foreach ($rows as $row) {
                $invoice->items()->create($row);
                StockService::move($row['product_colour_id'], -$row['qty'], 'sale', $row['unit_cost'], $invoice, 'Sold to ' . $customer->name, $data['date']);
            }

            if ($total <= 0) {
                $invoice->refreshPaid();
            }
            if ($paid > 0) {
                $alloc = PaymentService::oldestFirst('customer', $customer->id, $paid, $invoice->id);
                PaymentService::record('in', 'customer', $customer->id, $data['date'], $paymentLines, $alloc, 'Paid with ' . $invoice->number);
            }
            PaymentService::applyCredit('customer', $customer->id); // credit from returns / extra paid earlier

            return $invoice->fresh();
        });
    }

    /** Customer brings goods back. $qtys = [invoice_item_id => qty]. Pairs go back into stock. */
    public static function customerReturn(Invoice $invoice, array $qtys, array $data): GoodsReturn
    {
        return DB::transaction(function () use ($invoice, $qtys, $data) {
            $return = GoodsReturn::create([
                'number' => NumberService::next('RT'),
                'type' => 'customer',
                'invoice_id' => $invoice->id,
                'date' => $data['date'],
                'reason' => $data['reason'] ?? null,
                'user_id' => auth()->id(),
            ]);
            $factor = $invoice->subtotal > 0 ? $invoice->total / $invoice->subtotal : 1; // share of the bill discount
            $total = 0;
            $cost = 0;
            foreach ($invoice->items as $item) {
                $qty = min((int) ($qtys[$item->id] ?? 0), $item->qty - $item->returned_qty);
                if ($qty <= 0) {
                    continue;
                }
                $lineTotal = round($item->unit_price * $qty * $factor, 2);
                $unit = round($lineTotal / $qty, 2);
                $return->items()->create(['product_colour_id' => $item->product_colour_id, 'qty' => $qty, 'unit_price' => $unit, 'line_total' => $lineTotal]);
                StockService::move($item->product_colour_id, $qty, 'return', (float) $item->unit_cost, $return, 'Returned from ' . $invoice->number, $data['date']);
                $item->increment('returned_qty', $qty);
                $total += $lineTotal;
                $cost += $item->unit_cost * $qty;
            }
            if ($total <= 0) {
                throw ValidationException::withMessages(['qty' => 'Enter how many pairs came back.']);
            }
            $return->update(['total' => round($total, 2)]);
            $invoice->update([
                'returned_amount' => round($invoice->returned_amount + $total, 2),
                'cost_total' => max(0, round($invoice->cost_total - $cost, 2)),
            ]);
            $invoice->refreshPaid();
            PaymentService::freeExcess($invoice);                   // already paid? the extra becomes credit
            PaymentService::applyCredit('customer', $invoice->customer_id); // ...used on other unpaid bills first

            return $return;
        });
    }

    /** We send goods back to the supplier. $qtys = [purchase_item_id => qty]. Pairs leave stock. */
    public static function supplierReturn(Purchase $purchase, array $qtys, array $data): GoodsReturn
    {
        return DB::transaction(function () use ($purchase, $qtys, $data) {
            $return = GoodsReturn::create([
                'number' => NumberService::next('RS'),
                'type' => 'supplier',
                'purchase_id' => $purchase->id,
                'date' => $data['date'],
                'reason' => $data['reason'] ?? null,
                'user_id' => auth()->id(),
            ]);
            $factor = $purchase->subtotal > 0 ? ($purchase->subtotal - $purchase->discount) / $purchase->subtotal : 1;
            $total = 0;
            foreach ($purchase->items as $item) {
                $qty = min((int) ($qtys[$item->id] ?? 0), $item->qty - $item->returned_qty);
                if ($qty <= 0) {
                    continue;
                }
                $lineTotal = round($item->unit_price * $qty * $factor, 2);
                $unit = round($lineTotal / $qty, 2);
                $return->items()->create(['product_colour_id' => $item->product_colour_id, 'qty' => $qty, 'unit_price' => $unit, 'line_total' => $lineTotal]);
                StockService::move($item->product_colour_id, -$qty, 'purchase_return', (float) $item->unit_cost, $return, 'Sent back on ' . $purchase->number, $data['date']);
                $item->increment('returned_qty', $qty);
                $total += $lineTotal;
            }
            if ($total <= 0) {
                throw ValidationException::withMessages(['qty' => 'Enter how many pairs you are sending back.']);
            }
            $return->update(['total' => round($total, 2)]);
            $purchase->update(['returned_amount' => round($purchase->returned_amount + $total, 2)]);
            $purchase->refreshPaid();
            PaymentService::freeExcess($purchase);
            PaymentService::applyCredit('supplier', $purchase->supplier_id);

            return $return;
        });
    }
}
