<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class StockAdjustment extends Model
{
    protected $guarded = [];

    protected $casts = ['date' => 'date'];

    public const REASONS = [
        'damaged' => 'Damaged pairs',
        'damaged_writeoff' => 'Throw away damaged pairs',
        'lost' => 'Lost / missing',
        'found' => 'Found extra pairs',
        'supplier_return' => 'Sent back to supplier',
        'count' => 'Correct after counting',
    ];

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): MorphMany
    {
        return $this->morphMany(StockDocumentItem::class, 'document');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
