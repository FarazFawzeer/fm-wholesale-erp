<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Cheque;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\PurchaseOrder;

class CreditControlController extends Controller
{
    /** Overdue invoices, invoices coming due, and credit-limit usage per customer. */
    public function index()
    {
        $soon = now()->addDays((int) setting('reminder_days_before_due', 3));
        $open = Invoice::with('customer')->open()->orderBy('due_date')->get();

        $customers = Customer::with('group')->where('credit_limit', '>', 0)->get()->map(function ($c) {
            $balance = $c->balance();

            return ['customer' => $c, 'balance' => $balance, 'used' => $c->credit_limit > 0 ? $balance / $c->credit_limit * 100 : 0, 'overdue' => $c->overdueAmount()];
        })->sortByDesc('used');

        return view('finance.credit-control.index', [
            'overdue' => $open->filter(fn ($i) => $i->due_date->lt(now()->startOfDay())),
            'dueSoon' => $open->filter(fn ($i) => $i->due_date->between(now()->startOfDay(), $soon)),
            'customers' => $customers,
        ]);
    }

    /** What we must pay: supplier balances by due date and cheques we issued. */
    public function payables()
    {
        return view('finance.credit-control.payables', [
            'orders' => PurchaseOrder::with('supplier')->whereRaw('payable_amount - paid_amount > 0.009')->orderBy('due_date')->get(),
            'cheques' => Cheque::where('direction', 'issued')->whereIn('status', ['pending'])->orderBy('cheque_date')->get(),
            'inTransit' => PurchaseOrder::with('supplier')->whereIn('status', ['ordered', 'costed'])->orderBy('expected_date')->get(),
        ]);
    }
}
