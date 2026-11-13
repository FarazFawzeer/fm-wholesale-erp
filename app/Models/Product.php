<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** A shoe model: name + size range + photo. Stock is kept per colour (ProductColour). */
class Product extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean', 'cost_price' => 'float', 'selling_price' => 'float'];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function colours(): HasMany
    {
        return $this->hasMany(ProductColour::class)->orderBy('id');
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /** "30-42", "38" or "" */
    public function sizeLabel(): string
    {
        $from = trim((string) $this->size_from);
        $to = trim((string) $this->size_to);
        if ($from !== '' && $to !== '' && $from !== $to) {
            return $from . '-' . $to;
        }

        return $from !== '' ? $from : $to;
    }

    public function totalStock(): int
    {
        return (int) ($this->relationLoaded('colours') ? $this->colours->sum('qty') : $this->colours()->sum('qty'));
    }

    public function isLow(): bool
    {
        return $this->totalStock() <= $this->low_stock_level;
    }

    public function imageUrl(): ?string
    {
        return $this->image_path ? asset($this->image_path) : null;
    }

    /** "black 20 · white 10 · blue 30" */
    public function colourSummary(): string
    {
        return $this->colours->map(fn ($c) => $c->colour . ' ' . $c->qty)->implode(' · ');
    }

    public function marginPercent(): ?float
    {
        return $this->selling_price > 0 && $this->cost_price > 0
            ? round(($this->selling_price - $this->cost_price) / $this->selling_price * 100, 1)
            : null;
    }

    /** Next item number with the shop's prefix, e.g. ARD-1001 (prefix set in Settings). */
    public static function nextCode(): string
    {
        $prefix = strtoupper(trim((string) setting('item_prefix', 'ARD'))) ?: 'ARD';
        $max = static::where('code', 'like', $prefix . '-%')->pluck('code')
            ->map(fn ($c) => (int) preg_replace('/\D/', '', substr($c, strlen($prefix) + 1)))->max();
        $n = max(1000, (int) $max) + 1;
        while (static::where('code', $prefix . '-' . $n)->exists()) {
            $n++;
        }

        return $prefix . '-' . $n;
    }

    /** Data used by the item pickers on the purchase / sale screens. */
    public function toPicker(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'size' => $this->sizeLabel(),
            'brand' => $this->brand?->name,
            'category_id' => $this->category_id,
            'image' => $this->imageUrl(),
            'price' => (float) $this->selling_price,
            'cost' => (float) $this->cost_price,
            'colours' => $this->colours->map(fn ($c) => ['id' => $c->id, 'name' => $c->colour, 'qty' => (int) $c->qty, 'hex' => ProductColour::hex($c->colour)])->values(),
        ];
    }

    public static function pickerList(): array
    {
        return static::with(['colours', 'brand'])->where('is_active', true)->orderByRaw('(SELECT COALESCE(SUM(qty),0) FROM product_colours pc WHERE pc.product_id = products.id) <= 0')->orderBy('name')->get()->map->toPicker()->values()->all();
    }
}
