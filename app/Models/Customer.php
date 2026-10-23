<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean', 'credit_limit' => 'float', 'opening_balance' => 'float'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function creditNotes(): HasMany
    {
        return $this->hasMany(CreditNote::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'party_id')->where('party_type', 'customer');
    }

    public function cheques(): HasMany
    {
        return $this->hasMany(Cheque::class, 'party_id')->where('party_type', 'customer');
    }

    /** Outstanding = opening + invoiced - credit notes - receipts. */
    public function balance(): float
    {
        return (float) $this->opening_balance
            + (float) $this->invoices()->sum('total')
            - (float) $this->creditNotes()->sum('total')
            - (float) $this->payments()->sum('total');
    }

    public function overdueAmount(): float
    {
        return (float) $this->invoices()->overdue()->get()->sum(fn ($i) => $i->balance());
    }

    public function availableCredit(): float
    {
        return $this->credit_limit - $this->balance();
    }

    /** Post-dated cheques that have not cleared yet. */
    public function pendingChequesTotal(): float
    {
        return (float) $this->cheques()->whereIn('status', ['pending', 'deposited'])->sum('amount');
    }

    public function whatsappNumber(): ?string
    {
        $raw = preg_replace('/\D/', '', (string) ($this->whatsapp ?: $this->phone));
        if (! $raw) {
            return null;
        }

        return str_starts_with($raw, '0') ? '94' . substr($raw, 1) : $raw;
    }
}
