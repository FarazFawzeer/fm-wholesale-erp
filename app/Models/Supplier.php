<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean'];

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'party_id')->where('party_type', 'supplier');
    }

    public function cheques(): HasMany
    {
        return $this->hasMany(Cheque::class, 'party_id')->where('party_type', 'supplier');
    }

    /** What we owe the supplier: opening + received goods value - payments. */
    public function balance(): float
    {
        return (float) $this->opening_balance
            + (float) $this->purchaseOrders()->sum('payable_amount')
            - (float) $this->payments()->sum('total');
    }
}
