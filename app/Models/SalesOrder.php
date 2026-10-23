<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/** Quotations and sales orders share one table (type = quotation | order). */
class SalesOrder extends Model
{
    protected $guarded = [];

    protected $casts = ['date' => 'date', 'valid_until' => 'date'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function lines(): MorphMany
    {
        return $this->morphMany(SalesLine::class, 'document');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function convertedFrom(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'converted_from_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function label(): string
    {
        return $this->type === 'quotation' ? 'Quotation' : 'Sales Order';
    }
}
