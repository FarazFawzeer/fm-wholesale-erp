<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Money from a customer (in) or to a supplier (out), split across one or more methods. */
class Payment extends Model
{
    protected $guarded = [];

    protected $casts = ['date' => 'date', 'total' => 'float'];

    public const METHODS = [
        'cash' => 'Cash',
        'card' => 'Card',
        'online' => 'Online / Bank',
        'cheque' => 'Cheque',
    ];

    public const ICONS = [
        'cash' => 'solar:wad-of-money-outline',
        'card' => 'solar:card-outline',
        'online' => 'solar:smartphone-2-outline',
        'cheque' => 'solar:document-text-outline',
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
