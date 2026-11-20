<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductColour;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

/**
 * Every stock change goes through here, so the pairs on hand (product_colours.qty)
 * and the history (stock_movements) always agree.
 */
class StockService
{
    /** Add (+) or take out (-) pairs of one colour. Stops when stock would go below zero. */
    public static function move(int $colourId, int $qty, string $type, float $unitCost = 0,
        ?Model $reference = null, ?string $note = null, $date = null): void
    {
        if ($qty === 0) {
            return;
        }
        $colour = ProductColour::with('product')->lockForUpdate()->findOrFail($colourId);

        if ($colour->qty + $qty < 0) {
            throw ValidationException::withMessages([
                'stock' => "Not enough stock: {$colour->product->name} ({$colour->title}) has {$colour->qty} pairs, you need " . abs($qty) . '.',
            ]);
        }

        $colour->qty += $qty;
        $colour->save();

        StockMovement::create([
            'date' => $date ?? now()->toDateString(),
            'product_colour_id' => $colourId,
            'type' => $type,
            'qty' => $qty,
            'balance' => $colour->qty,
            'unit_cost' => $unitCost,
            'reference_type' => $reference ? $reference->getMorphClass() : null,
            'reference_id' => $reference?->getKey(),
            'reference_no' => $reference->number ?? null,
            'note' => $note,
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Pairs coming in at a cost (purchase / opening stock): the product cost
     * becomes the average of the old stock and the new pairs.
     */
    public static function receive(int $colourId, int $qty, float $unitCost, ?Model $reference = null,
        ?string $note = null, $date = null, string $type = 'purchase'): void
    {
        $product = Product::lockForUpdate()->find(ProductColour::whereKey($colourId)->value('product_id'));
        $onHand = max(0, (int) $product->colours()->sum('qty'));
        $newCost = ($onHand + $qty) > 0
            ? (($onHand * (float) $product->cost_price) + ($qty * $unitCost)) / ($onHand + $qty)
            : $unitCost;
        $product->update(['cost_price' => round($newCost, 2)]);

        static::move($colourId, $qty, $type, $unitCost, $reference, $note, $date);
    }

    /** Find a colour of a product by name, or create it (purchases may bring new colours). */
    public static function colour(Product $product, string $name, ?string $size = null): ProductColour
    {
        $name = trim(preg_replace('/\s+/', ' ', $name));
        $size = ProductColour::cleanSize($size);

        return $product->colours()->whereRaw('LOWER(colour) = ?', [mb_strtolower($name)])
            ->when($size, fn ($q) => $q->whereRaw('LOWER(size) = ?', [mb_strtolower($size)]), fn ($q) => $q->whereNull('size'))->first()
            ?? $product->colours()->create(['colour' => ucwords($name), 'size' => $size, 'qty' => 0]);
    }
}
