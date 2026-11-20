<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** One colour (+ optional size range) of a product, with its stock in pairs. */
class ProductColour extends Model
{
    protected $guarded = [];

    protected $casts = ['qty' => 'integer'];

    public const COMMON = ['Black', 'White', 'Brown', 'Tan', 'Blue', 'Navy', 'Grey', 'Red', 'Maroon', 'Green', 'Beige', 'Pink', 'Gold', 'Silver', 'Multi'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function label(): string
    {
        return $this->product->name . ' - ' . $this->title;
    }

    /** "Black" or, when the line has a size range, "Black (5-7)". */
    public function getTitleAttribute(): string
    {
        return $this->colour . (filled($this->size) ? ' (' . $this->size . ')' : '');
    }

    /** Tidy a typed size range: "5 - 7" -> "5-7", "11 to 1" -> "11-1". */
    public static function cleanSize(?string $size): ?string
    {
        $size = trim(preg_replace('/\s*(?:-|–|to)\s*/iu', '-', trim((string) $size)));

        return $size === '' ? null : mb_substr($size, 0, 20);
    }

    /** A colour dot for common colour names; grey for anything unknown. */
    public static function hex(string $name): string
    {
        $map = [
            'black' => '#111827', 'white' => '#ffffff', 'brown' => '#7c4a1e', 'tan' => '#d2a26b', 'blue' => '#2563eb',
            'navy' => '#1e3a8a', 'grey' => '#9ca3af', 'gray' => '#9ca3af', 'red' => '#dc2626', 'maroon' => '#7f1d1d',
            'green' => '#16a34a', 'olive' => '#65743a', 'beige' => '#e8d9b5', 'cream' => '#f5f0dc', 'pink' => '#ec4899',
            'purple' => '#7c3aed', 'yellow' => '#facc15', 'orange' => '#f97316', 'gold' => '#d4a017', 'silver' => '#c0c0c0',
            'khaki' => '#c3b091', 'camel' => '#c19a6b', 'coffee' => '#6f4e37', 'wine' => '#722f37', 'peach' => '#ffcba4',
        ];
        $key = strtolower(trim($name));
        foreach ($map as $word => $hex) {
            if ($key === $word || str_contains($key, $word)) {
                return $hex;
            }
        }

        return '#cbd5e1';
    }
}
