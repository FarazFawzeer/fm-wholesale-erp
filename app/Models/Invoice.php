<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** A sale to a customer. Saving it takes the pairs out of stock. There are no due dates. */
class Invoice extends Model
{
    protected $guarded = [];

    protected $casts = ['date' => 'date', 'total' => 'float', 'paid_amount' => 'float', 'returned_amount' => 'float', 'cost_total' => 'float'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(GoodsReturn::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnpaid(Builder $q): Builder
    {
        return $q->whereRaw('total - returned_amount - paid_amount > 0.009');
    }

    public function balance(): float
    {
        return max(0, round($this->total - $this->returned_amount - $this->paid_amount, 2));
    }

    public function daysUnpaid(): int
    {
        return (int) $this->date->diffInDays(now()->startOfDay());
    }

    public function refreshPaid(): void
    {
        $paid = round((float) $this->allocations()->sum('amount'), 2);
        $net = $this->total - $this->returned_amount;
        $this->update([
            'paid_amount' => $paid,
            'payment_status' => $net - $paid <= 0.009 ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
        ]);
    }

    public function profit(): float
    {
        return round(($this->total - $this->returned_amount) - $this->cost_total, 2);
    }

    public function totalPairs(): int
    {
        return (int) $this->items->sum('qty');
    }

    /** Link the customer can open without logging in. */
    public function shareUrl(): string
    {
        return route('invoice.public', $this->share_token);
    }

    public function whatsappText(): string
    {
        $lines = [
            'Dear ' . $this->customer->name . ',',
            'Thank you for your order. Invoice ' . $this->number . ' (' . $this->date->format('d M Y') . ')',
            'Total: ' . money($this->total - $this->returned_amount),
        ];
        if ($this->paid_amount > 0) {
            $lines[] = 'Paid: ' . money($this->paid_amount);
        }
        if ($this->balance() > 0) {
            $lines[] = 'Balance to pay: ' . money($this->balance());
        }
        $lines[] = 'View invoice: ' . $this->shareUrl();
        $lines[] = '- ' . setting('company_name');

        return implode("\n", $lines);
    }
}
