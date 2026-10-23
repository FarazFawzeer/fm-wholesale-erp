<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** A customer receipt (in) or supplier payment (out), split across one or more methods. */
class Payment extends Model
{
    protected $guarded = [];

    protected $casts = ['date' => 'date'];

    public const METHODS = [
        'cash' => 'Cash',
        'bank_transfer' => 'Bank transfer',
        'card' => 'Card',
        'cheque' => 'Cheque',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(PaymentLine::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function party(): ?Model
    {
        return $this->party_type === 'customer' ? Customer::find($this->party_id) : Supplier::find($this->party_id);
    }

    public function getPartyNameAttribute(): string
    {
        return $this->party()?->name ?? '-';
    }

    public function methodSummary(): string
    {
        return $this->lines->pluck('method')->unique()->map(fn ($m) => self::METHODS[$m] ?? $m)->implode(' + ');
    }
}
