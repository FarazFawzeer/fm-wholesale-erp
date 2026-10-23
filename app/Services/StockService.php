<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

/**
 * Every stock change goes through here so the `stocks` balance and the
 * `stock_movements` ledger never drift apart.
 */
class StockService
{
    public static function available(int $variantId, int $warehouseId): int
    {
        return (int) Stock::where('product_variant_id', $variantId)->where('warehouse_id', $warehouseId)->value('qty');
    }

    /**
     * Move sellable stock (+ in / - out). Throws when going negative.
     */
    public static function move(int $variantId, int $warehouseId, int $qty, string $type, float $unitCost = 0,
        ?Model $reference = null, ?string $note = null, $date = null): void
    {
        if ($qty === 0) {
            return;
        }

        $stock = Stock::firstOrCreate(['product_variant_id' => $variantId, 'warehouse_id' => $warehouseId]);
        $stock = Stock::whereKey($stock->id)->lockForUpdate()->first();

        if ($stock->qty + $qty < 0) {
            $variant = ProductVariant::with('product')->find($variantId);
            throw ValidationException::withMessages([
                'stock' => "Not enough stock for {$variant->product->name} size {$variant->size}. Available: {$stock->qty}, needed: " . abs($qty),
            ]);
        }

        $stock->increment('qty', $qty);
        static::log($variantId, $warehouseId, $qty, $type, $unitCost, $reference, $note, $date);
    }

    /** Move damaged (non-sellable) stock. */
    public static function moveDamaged(int $variantId, int $warehouseId, int $qty, string $type, float $unitCost = 0,
        ?Model $reference = null, ?string $note = null, $date = null): void
    {
        if ($qty === 0) {
            return;
        }

        $stock = Stock::firstOrCreate(['product_variant_id' => $variantId, 'warehouse_id' => $warehouseId]);
        if ($stock->damaged_qty + $qty < 0) {
            throw ValidationException::withMessages(['stock' => 'Not enough damaged stock to write off.']);
        }
        $stock->increment('damaged_qty', $qty);
        static::log($variantId, $warehouseId, $qty, $type, $unitCost, $reference, $note, $date);
    }

    /**
     * Receive stock at a new unit cost and re-average the product cost
     * (weighted average across all sizes and warehouses).
     */
    public static function receive(int $variantId, int $warehouseId, int $qty, float $unitCost,
        ?Model $reference = null, ?string $note = null, $date = null, string $type = 'grn'): void
    {
        $product = Product::lockForUpdate()->find(ProductVariant::whereKey($variantId)->value('product_id'));
        $onHand = max(0, $product->totalStock());
        $newCost = ($onHand + $qty) > 0
            ? (($onHand * (float) $product->cost_price) + ($qty * $unitCost)) / ($onHand + $qty)
            : $unitCost;
        $product->update(['cost_price' => round($newCost, 2)]);

        static::move($variantId, $warehouseId, $qty, $type, $unitCost, $reference, $note, $date);
    }

    protected static function log(int $variantId, int $warehouseId, int $qty, string $type, float $unitCost,
        ?Model $reference, ?string $note, $date): void
    {
        StockMovement::create([
            'date' => $date ?? now()->toDateString(),
            'product_variant_id' => $variantId,
            'warehouse_id' => $warehouseId,
            'type' => $type,
            'qty' => $qty,
            'unit_cost' => $unitCost,
            'reference_type' => $reference ? $reference->getMorphClass() : null,
            'reference_id' => $reference?->getKey(),
            'reference_no' => $reference->number ?? null,
            'note' => $note,
            'user_id' => auth()->id(),
        ]);
    }
}
