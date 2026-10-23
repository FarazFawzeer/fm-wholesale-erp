<?php

namespace App\Support;

use App\Models\Cheque;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\PurchaseOrder;
use Illuminate\Support\Collection;

/** Business alerts shown in the topbar bell and on the dashboard. */
class Alerts
{
    protected static ?Collection $cache = null;

    public static function forTopbar(): Collection
    {
        if (static::$cache) {
            return static::$cache;
        }
        $alerts = collect();
        $soon = now()->addDays((int) setting('reminder_days_before_due', 3))->toDateString();

        $overdue = Invoice::overdue()->get();
        if ($overdue->count()) {
            $alerts->push(['title' => $overdue->count() . ' overdue invoices', 'text' => money($overdue->sum(fn ($i) => $i->balance())) . ' to collect',
                'icon' => 'solar:bill-cross-outline', 'color' => 'danger', 'url' => route('credit-control.index')]);
        }

        $chequesIn = Cheque::where('direction', 'received')->whereIn('status', ['pending', 'deposited'])->whereDate('cheque_date', '<=', $soon)->get();
        if ($chequesIn->count()) {
            $alerts->push(['title' => $chequesIn->count() . ' customer cheques to deposit', 'text' => money($chequesIn->sum('amount')) . ' due within ' . setting('reminder_days_before_due') . ' days',
                'icon' => 'solar:card-recive-outline', 'color' => 'info', 'url' => route('cheques.index', ['direction' => 'received', 'status' => 'open'])]);
        }

        $chequesOut = Cheque::where('direction', 'issued')->whereIn('status', ['pending'])->whereDate('cheque_date', '<=', now()->addDays(10))->get();
        if ($chequesOut->count()) {
            $alerts->push(['title' => $chequesOut->count() . ' issued cheques coming due', 'text' => 'Keep ' . money($chequesOut->sum('amount')) . ' in the bank',
                'icon' => 'solar:card-send-outline', 'color' => 'warning', 'url' => route('cheques.index', ['direction' => 'issued', 'status' => 'open'])]);
        }

        $supplierDue = PurchaseOrder::whereRaw('payable_amount - paid_amount > 0.009')->whereDate('due_date', '<=', now()->addDays(7))->count();
        if ($supplierDue) {
            $alerts->push(['title' => $supplierDue . ' supplier payments due', 'text' => 'Due within 7 days or overdue',
                'icon' => 'solar:globus-outline', 'color' => 'warning', 'url' => route('credit-control.payables')]);
        }

        $low = static::lowStockCount();
        if ($low) {
            $alerts->push(['title' => $low . ' products low on stock', 'text' => 'Below re-order level',
                'icon' => 'solar:box-minimalistic-outline', 'color' => 'secondary', 'url' => route('reports.low-stock')]);
        }

        return static::$cache = $alerts;
    }

    public static function lowStockCount(): int
    {
        return Product::where('is_active', true)
            ->withSum('stocks as on_hand', 'qty')
            ->get()
            ->filter(fn ($p) => (int) $p->on_hand <= $p->reorder_level)
            ->count();
    }
}
