<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentLine extends Model
{
    protected $guarded = [];

    protected $casts = ['amount' => 'float'];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function cheque(): BelongsTo
    {
        return $this->belongsTo(Cheque::class);
    }

    public function methodLabel(): string
    {
        return Payment::METHODS[$this->method] ?? ucfirst($this->method);
    }
}
