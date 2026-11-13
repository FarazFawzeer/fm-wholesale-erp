<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $guarded = [];

    protected $casts = ['is_active' => 'boolean', 'opening_balance' => 'float'];

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'party_id')->where('party_type', 'customer');
    }

    public function cheques(): HasMany
    {
        return $this->hasMany(Cheque::class, 'party_id')->where('party_type', 'customer');
    }

    /** Adds a `due` column: what the customer still owes us (old balance + bills - returns - payments). */
    public function scopeWithDue(Builder $query): Builder
    {
        return $query->select('customers.*')->selectRaw(
            'customers.opening_balance'
            . ' + COALESCE((SELECT SUM(i.total - i.returned_amount) FROM invoices i WHERE i.customer_id = customers.id), 0)'
            . " - COALESCE((SELECT SUM(p.total) FROM payments p WHERE p.party_type = 'customer' AND p.party_id = customers.id), 0) AS due"
        );
    }

    public function balance(): float
    {
        $bills = (float) $this->invoices()->sum('total') - (float) $this->invoices()->sum('returned_amount');

        return round($this->opening_balance + $bills - (float) $this->payments()->sum('total'), 2);
    }

    /** Part of the opening (old) balance that is still not paid. */
    public function openingDue(): float
    {
        $paid = PaymentAllocation::whereNull('invoice_id')->whereNull('purchase_id')
            ->whereHas('payment', fn ($q) => $q->where('party_type', 'customer')->where('party_id', $this->id))
            ->sum('amount');

        return max(0, round($this->opening_balance - $paid, 2));
    }

    public function unpaidInvoices()
    {
        return $this->invoices()->unpaid()->orderBy('date')->orderBy('id')->get();
    }

    /** Date of the oldest bill that is not fully paid (null when nothing is owed). */
    public function oldestUnpaidDate(): ?\Carbon\Carbon
    {
        $date = $this->invoices()->unpaid()->min('date');
        if (! $date && $this->openingDue() > 0) {
            return $this->created_at;
        }

        return $date ? \Carbon\Carbon::parse($date) : null;
    }

    public function label(): string
    {
        return $this->name . ($this->shop_name ? ' (' . $this->shop_name . ')' : '') . ($this->city ? ' - ' . $this->city : '');
    }

    /** Phone in WhatsApp format: 0771234567 -> 94771234567 */
    public function whatsappNumber(): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) ($this->whatsapp ?: $this->phone));
        if ($digits === '') {
            return null;
        }
        if (str_starts_with($digits, '0')) {
            $digits = '94' . substr($digits, 1);
        } elseif (strlen($digits) === 9) {
            $digits = '94' . $digits;
        }

        return $digits;
    }
}
