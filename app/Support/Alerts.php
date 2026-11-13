<?php

namespace App\Support;

use App\Models\Cheque;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Collection;

/** Reminders shown in the topbar bell. */
class Alerts
{
    protected static ?Collection $cache = null;

    protected static ?int $owing = null;

    /** Number of customers who still owe money. */
    public static function owingCount(): int
    {
        static::forTopbar();

        return (int) static::$owing;
    }

    public static function forTopbar(): Collection
    {
        if (static::$cache) {
            return static::$cache;
        }
        $alerts = collect();

        // Customers who have not paid - stays until they pay (no due dates)
        $owing = Customer::withDue()->get()->filter(fn ($c) => $c->due > 0.009);
        static::$owing = $owing->count();
        if ($owing->count()) {
            $alerts->push(['title' => $owing->count() . ' ' . str('customer')->plural($owing->count()) . ' not paid', 'text' => money($owing->sum('due')) . ' to collect',
                'icon' => 'solar:hand-money-outline', 'color' => 'danger', 'url' => route('dues')]);
        }

        $chequesIn = Cheque::where('direction', 'received')->where('status', 'pending')->whereDate('cheque_date', '<=', now()->addDays(2))->get();
        if ($chequesIn->count()) {
            $alerts->push(['title' => $chequesIn->count() . ' customer ' . str('cheque')->plural($chequesIn->count()) . ' to deposit', 'text' => money($chequesIn->sum('amount')),
                'icon' => 'solar:card-recive-outline', 'color' => 'info', 'url' => route('cheques', ['direction' => 'received'])]);
        }

        $chequesOut = Cheque::where('direction', 'issued')->where('status', 'pending')->whereDate('cheque_date', '<=', now()->addDays(5))->get();
        if ($chequesOut->count()) {
            $alerts->push(['title' => $chequesOut->count() . ' of our cheques coming due', 'text' => 'Keep ' . money($chequesOut->sum('amount')) . ' in the bank',
                'icon' => 'solar:card-send-outline', 'color' => 'warning', 'url' => route('cheques', ['direction' => 'issued'])]);
        }

        $low = Product::with('colours')->where('is_active', true)->get()->filter->isLow()->count();
        if ($low) {
            $alerts->push(['title' => $low . ' ' . str('product')->plural($low) . ' low on stock', 'text' => 'Time to buy more',
                'icon' => 'solar:box-minimalistic-outline', 'color' => 'secondary', 'url' => route('reports.stock', ['show' => 'low'])]);
        }

        return static::$cache = $alerts;
    }
}
