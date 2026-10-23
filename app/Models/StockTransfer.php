<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class StockTransfer extends Model
{
    protected $guarded = [];

    protected $casts = ['date' => 'date'];

    public function from(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function to(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
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
