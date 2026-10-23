<?php

namespace App\Services;

use App\Models\Grn;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseService
{
    /**
     * Save PO lines from the size-grid form:
     * [['product_id' => 1, 'unit_price' => 12.5, 'qty' => [variant_id => pairs, ...]], ...]
     */
    public static function syncItems(PurchaseOrder $po, array $lines): void
    {
        $po->items()->delete();
        foreach ($lines as $line) {
            foreach (($line['qty'] ?? []) as $variantId => $qty) {
                if ((int) $qty <= 0) {
                    continue;
                }
                $variant = ProductVariant::where('product_id', $line['product_id'])->findOrFail($variantId);
                $po->items()->create([
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'qty' => (int) $qty,
                    'unit_price_fx' => (float) $line['unit_price'],
                ]);
            }
        }
        if (! $po->items()->exists()) {
            throw ValidationException::withMessages(['lines' => 'Add at least one product with a quantity.']);
        }
        $po->recalculate();
    }

    public static function syncCharges(PurchaseOrder $po, array $charges): void
    {
        $po->charges()->delete();
        foreach ($charges as $charge) {
            if ((float) ($charge['amount'] ?? 0) > 0) {
                $po->charges()->create([
                    'type' => $charge['type'],
                    'description' => $charge['description'] ?? null,
                    'amount' => (float) $charge['amount'],
                ]);
            }
        }
        $po->recalculate();
    }

    /** Create a draft GRN from a PO with every outstanding quantity pre-filled. */
    public static function draftGrn(PurchaseOrder $po, int $warehouseId, string $date): Grn
    {
        return DB::transaction(function () use ($po, $warehouseId, $date) {
            $grn = Grn::create([
                'number' => NumberService::next('GRN'),
                'purchase_order_id' => $po->id,
                'supplier_id' => $po->supplier_id,
                'warehouse_id' => $warehouseId,
                'received_date' => $date,
                'status' => 'draft',
                'user_id' => auth()->id(),
            ]);
            foreach ($po->items as $item) {
                $outstanding = $item->qty - $item->received_qty;
                if ($outstanding > 0) {
                    $grn->items()->create([
                        'purchase_order_item_id' => $item->id,
                        'product_variant_id' => $item->product_variant_id,
                        'ordered_qty' => $outstanding,
                        'received_qty' => $outstanding,
                        'damaged_qty' => 0,
                        'unit_cost' => $item->landed_unit_cost,
                    ]);
                }
            }

            return $grn;
        });
    }

    /**
     * Post a GRN: good pairs go into sellable stock at landed cost (re-averaging the
     * product cost), damaged pairs are held separately, supplier payable is raised.
     */
    public static function postGrn(Grn $grn): void
    {
        if ($grn->status === 'posted') {
            throw ValidationException::withMessages(['grn' => 'This GRN is already posted.']);
        }

        DB::transaction(function () use ($grn) {
            $grn->load('items.poItem', 'purchaseOrder.supplier');
            $po = $grn->purchaseOrder;
            $value = 0;
            $supplierValue = 0;

            foreach ($grn->items as $item) {
                if ($item->damaged_qty > $item->received_qty) {
                    throw ValidationException::withMessages(['items' => 'Damaged quantity cannot be more than received quantity.']);
                }
                $good = $item->received_qty - $item->damaged_qty;
                if ($good > 0) {
                    StockService::receive($item->product_variant_id, $grn->warehouse_id, $good, (float) $item->unit_cost,
                        $grn, 'Received from ' . $po->number, $grn->received_date);
                }
                if ($item->damaged_qty > 0) {
                    StockService::moveDamaged($item->product_variant_id, $grn->warehouse_id, $item->damaged_qty, 'grn_damaged',
                        (float) $item->unit_cost, $grn, 'Damaged on arrival', $grn->received_date);
                }
                $item->poItem->increment('received_qty', $item->received_qty);
                $value += $item->received_qty * $item->unit_cost;
                $supplierValue += $item->received_qty * $item->poItem->unit_price_fx * $po->exchange_rate;
            }

            $grn->update(['status' => 'posted', 'posted_at' => now(), 'total_value' => round($value, 2)]);

            $po->load('items');
            $fully = $po->items->every(fn ($i) => $i->received_qty >= $i->qty);
            $po->update([
                'status' => $fully ? 'received' : 'partial',
                'payable_amount' => round($po->payable_amount + $supplierValue, 2),
                'due_date' => $po->due_date ?? $grn->received_date->copy()->addDays($po->supplier->payment_terms_days),
            ]);
        });
    }
}
