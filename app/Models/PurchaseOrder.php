<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $guarded = [];

    protected $casts = ['order_date' => 'date', 'expected_date' => 'date', 'due_date' => 'date'];

    public const CHARGE_TYPES = [
        'freight' => 'Freight',
        'insurance' => 'Insurance',
        'duty' => 'Customs duty',
        'clearing' => 'Clearing & port charges',
        'transport' => 'Local transport',
        'other' => 'Other charges',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function charges(): HasMany
    {
        return $this->hasMany(PurchaseOrderCharge::class);
    }

    public function grns(): HasMany
    {
        return $this->hasMany(Grn::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supplierCostLkr(): float
    {
        return round($this->subtotal_fx * $this->exchange_rate, 2);
    }

    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'ordered', 'costed']);
    }

    public function canReceive(): bool
    {
        return in_array($this->status, ['ordered', 'costed', 'partial']);
    }

    public function balance(): float
    {
        return (float) $this->payable_amount - (float) $this->paid_amount;
    }

    /**
     * Landed cost = supplier price (converted to LKR) + share of freight, insurance,
     * duty and other charges, allocated by value or by quantity.
     */
    public function recalculate(): void
    {
        $this->load('items', 'charges');
        $subtotalFx = $this->items->sum(fn ($i) => $i->qty * $i->unit_price_fx);
        $charges = (float) $this->charges->sum('amount');
        $totalQty = max(1, $this->items->sum('qty'));

        foreach ($this->items as $item) {
            $base = $item->unit_price_fx * $this->exchange_rate;
            $share = $this->allocation_method === 'qty'
                ? $charges / $totalQty
                : ($subtotalFx > 0 ? $charges * ($item->unit_price_fx / $subtotalFx) : 0);
            $item->update(['landed_unit_cost' => round($base + $share, 2)]);
        }

        $this->update([
            'subtotal_fx' => round($subtotalFx, 2),
            'charges_total' => $charges,
            'landed_total' => round($subtotalFx * $this->exchange_rate + $charges, 2),
        ]);
    }
}
