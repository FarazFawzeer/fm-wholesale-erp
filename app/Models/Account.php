<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Cash in hand, bank accounts and card settlement accounts. */
class Account extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean', 'opening_balance' => 'float'];

    public function transactions(): HasMany
    {
        return $this->hasMany(AccountTransaction::class);
    }

    public function balance($upToDate = null): float
    {
        return (float) $this->opening_balance
            + (float) $this->transactions()->when($upToDate, fn ($q) => $q->whereDate('date', '<=', $upToDate))->sum('amount');
    }
}
