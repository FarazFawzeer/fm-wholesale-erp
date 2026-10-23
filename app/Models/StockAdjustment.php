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
        'count' => 'Stock count correction',
        'damaged' => 'Mark as damaged',
        'damaged_writeoff' => 'Write off damaged',
        'lost' => 'Lost / missing',
        'found' => 'Found / surplus',
        'supplier_return' => 'Return to supplier',
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
