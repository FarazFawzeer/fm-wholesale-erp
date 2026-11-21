<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    protected $guarded = [];

    /** Quick reasons for pairs that did not arrive. */
    public const REASONS = ['Damaged', 'Customs took', 'Missing from box', 'Wrong size / colour', 'Other'];

    protected $casts = ['qty' => 'integer', 'missing_qty' => 'integer', 'returned_qty' => 'integer'];

    /** Pairs that really arrived (billed pairs minus damaged / missing). */
    public function receivedQty(): int
    {
        return $this->qty - $this->missing_qty;
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function colour(): BelongsTo
    {
        return $this->belongsTo(ProductColour::class, 'product_colour_id');
    }
}
