<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Goods back from a customer (type customer) or sent back to a supplier (type supplier). */
class GoodsReturn extends Model
{
    protected $table = 'returns';

    protected $guarded = [];

    protected $casts = ['date' => 'date', 'total' => 'float'];

    public function items(): HasMany
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function partyName(): string
    {
        return $this->type === 'customer' ? ($this->invoice?->customer?->name ?? '-') : ($this->purchase?->supplier?->name ?? '-');
    }
}
