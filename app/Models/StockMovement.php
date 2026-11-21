<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    protected $guarded = [];

    protected $casts = ['date' => 'date'];

    public const TYPES = [
        'opening' => 'Opening stock',
        'purchase' => 'Bought',
        'sale' => 'Sold',
        'return' => 'Customer return',
        'purchase_return' => 'Returned to supplier',
        'correction' => 'Stock correction',
        'missing' => 'Missing / damaged on arrival',
    ];

    public function colour(): BelongsTo
    {
        return $this->belongsTo(ProductColour::class, 'product_colour_id');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }
}
