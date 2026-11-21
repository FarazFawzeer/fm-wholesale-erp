<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Goods bought from a supplier. Saving it adds the pairs to stock. */
class Purchase extends Model
{
    protected $guarded = [];

    protected $casts = ['date' => 'date', 'total' => 'float', 'paid_amount' => 'float', 'returned_amount' => 'float'];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(GoodsReturn::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnpaid(Builder $q): Builder
    {
        return $q->whereRaw('total - returned_amount - paid_amount > 0.009');
    }

    public function balance(): float
    {
        return max(0, round($this->total - $this->returned_amount - $this->paid_amount, 2));
    }

    public function status(): string
    {
        return $this->balance() <= 0 ? 'paid' : ($this->paid_amount > 0 ? 'partial' : 'unpaid');
    }

    public function refreshPaid(): void
    {
        $this->update(['paid_amount' => round((float) $this->allocations()->sum('amount'), 2)]);
    }

    public function totalPairs(): int
    {
        return (int) $this->items->sum('qty');
    }

    /** Pairs paid for but not received (damaged, customs, missing). */
    public function missingPairs(): int
    {
        return (int) $this->items->sum('missing_qty');
    }
}
