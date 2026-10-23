<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cheque extends Model
{
    protected $guarded = [];

    protected $casts = ['cheque_date' => 'date', 'status_date' => 'date', 'amount' => 'float'];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function party(): ?Model
    {
        return $this->party_type === 'customer' ? Customer::find($this->party_id) : Supplier::find($this->party_id);
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['pending', 'deposited']);
    }

    public function isPostDated(): bool
    {
        return $this->isOpen() && $this->cheque_date->isFuture();
    }
}
