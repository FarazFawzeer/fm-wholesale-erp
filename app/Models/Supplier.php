<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean', 'opening_balance' => 'float'];

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'party_id')->where('party_type', 'supplier');
    }

    /** Adds a `due` column: what we still owe this supplier. */
    public function scopeWithDue(Builder $query): Builder
    {
        return $query->select('suppliers.*')->selectRaw(
            'suppliers.opening_balance'
            . ' + COALESCE((SELECT SUM(b.total - b.returned_amount) FROM purchases b WHERE b.supplier_id = suppliers.id), 0)'
            . " - COALESCE((SELECT SUM(p.total) FROM payments p WHERE p.party_type = 'supplier' AND p.party_id = suppliers.id), 0) AS due"
        );
    }

    public function balance(): float
    {
        $bills = (float) $this->purchases()->sum('total') - (float) $this->purchases()->sum('returned_amount');

        return round($this->opening_balance + $bills - (float) $this->payments()->sum('total'), 2);
    }

    public function openingDue(): float
    {
        $paid = PaymentAllocation::whereNull('invoice_id')->whereNull('purchase_id')
            ->whereHas('payment', fn ($q) => $q->where('party_type', 'supplier')->where('party_id', $this->id))
            ->sum('amount');

        return max(0, round($this->opening_balance - $paid, 2));
    }

    public function unpaidPurchases()
    {
        return $this->purchases()->unpaid()->orderBy('date')->orderBy('id')->get();
    }

    public function label(): string
    {
        return $this->name . ($this->phone ? ' - ' . $this->phone : '');
    }
}
