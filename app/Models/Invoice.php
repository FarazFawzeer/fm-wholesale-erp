<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Invoice extends Model
{
    protected $guarded = [];

    protected $casts = ['date' => 'date', 'due_date' => 'date'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function salesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function lines(): MorphMany
    {
        return $this->morphMany(SalesLine::class, 'document');
    }

    public function deliveryNotes(): HasMany
    {
        return $this->hasMany(DeliveryNote::class);
    }

    public function creditNotes(): HasMany
    {
        return $this->hasMany(CreditNote::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOpen(Builder $q): Builder
    {
        return $q->whereRaw('total - paid_amount - credited_amount > 0.009');
    }

    public function scopeOverdue(Builder $q): Builder
    {
        return $q->open()->whereDate('due_date', '<', now()->toDateString());
    }

    public function balance(): float
    {
        return round((float) $this->total - (float) $this->paid_amount - (float) $this->credited_amount, 2);
    }

    public function profit(): float
    {
        return (float) $this->total - (float) $this->cost_total;
    }

    public function daysOverdue(): int
    {
        return $this->balance() > 0 && $this->due_date->isPast() ? (int) $this->due_date->diffInDays(now()) : 0;
    }

    /** Recompute paid / credited / status after a receipt, credit note or bounced cheque. */
    public function refreshPaymentStatus(): void
    {
        $paid = (float) $this->allocations()->sum('amount');
        $credited = (float) $this->creditNotes()->sum('total');
        $balance = $this->total - $paid - $credited;
        $this->update([
            'paid_amount' => $paid,
            'credited_amount' => $credited,
            'payment_status' => $balance <= 0.009 ? 'paid' : (($paid + $credited) > 0 ? 'partial' : 'unpaid'),
        ]);
    }

    public function refreshDeliveryStatus(): void
    {
        $this->load('lines');
        $ordered = $this->lines->sum('qty');
        $delivered = $this->lines->sum('delivered_qty');
        $this->update([
            'delivery_status' => $delivered <= 0 ? 'pending' : ($delivered >= $ordered ? 'delivered' : 'partial'),
        ]);
    }
}
