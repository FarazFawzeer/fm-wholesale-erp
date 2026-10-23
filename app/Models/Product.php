<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Product extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'cost_price' => 'float',
        'selling_price' => 'float',
        'min_price' => 'float',
        'markup_percent' => 'float',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderByRaw('CAST(size AS DECIMAL(6,1))');
    }

    public function stocks(): HasManyThrough
    {
        return $this->hasManyThrough(Stock::class, ProductVariant::class);
    }

    public function priceListItems(): HasMany
    {
        return $this->hasMany(PriceListItem::class);
    }

    public function totalStock(?int $warehouseId = null): int
    {
        return (int) $this->stocks()->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))->sum('qty');
    }

    public function getDisplayNameAttribute(): string
    {
        return trim(($this->brand?->name ? $this->brand->name . ' ' : '') . $this->name . ($this->colour ? ' - ' . $this->colour : ''));
    }

    /** Markup rule: product override > category rule > company default. */
    public function effectiveMarkup(): float
    {
        return (float) ($this->markup_percent ?? $this->category?->markup_percent ?? setting('default_markup_percent'));
    }

    public function suggestedPrice(): float
    {
        return round($this->cost_price * (1 + $this->effectiveMarkup() / 100), -1);
    }

    /** Lowest allowed selling price per pair. */
    public function minimumPrice(): float
    {
        if ($this->min_price) {
            return (float) $this->min_price;
        }

        return round($this->cost_price * (1 + setting('min_margin_percent') / 100), 2);
    }

    /** Price for a customer group: price list override, else group discount off the wholesale price. */
    public function priceForGroup(?CustomerGroup $group): float
    {
        if (! $group) {
            return (float) $this->selling_price;
        }
        $listed = $this->priceListItems->firstWhere('customer_group_id', $group->id);
        if ($listed) {
            return (float) $listed->price;
        }

        return round($this->selling_price * (1 - $group->discount_percent / 100), 2);
    }

    public function marginPercent(): float
    {
        return $this->selling_price > 0 ? (($this->selling_price - $this->cost_price) / $this->selling_price) * 100 : 0;
    }
}
