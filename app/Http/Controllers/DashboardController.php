<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Cheque;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Stock;
use App\Support\Alerts;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();

        $todaySales = Invoice::whereDate('date', $today)->sum('total');
        $month = Invoice::whereDate('date', '>=', $monthStart)->selectRaw('SUM(total) total, SUM(cost_total) cost, COUNT(*) n')->first();
        $monthExpenses = Expense::whereDate('date', '>=', $monthStart)->sum('amount');

        $receivable = Invoice::open()->get()->sum(fn ($i) => $i->balance());
        $overdue = Invoice::overdue()->get();
        $payable = (float) PurchaseOrder::sum(DB::raw('payable_amount - paid_amount'));

        $stockValue = (float) Stock::join('product_variants', 'product_variants.id', '=', 'stocks.product_variant_id')
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->sum(DB::raw('stocks.qty * products.cost_price'));
        $stockPairs = (int) Stock::sum('qty');

        $accounts = Account::where('is_active', true)->get()->map(fn ($a) => ['name' => $a->name, 'type' => $a->type, 'balance' => $a->balance()]);

        // Sales vs profit, last 30 days
        $daily = Invoice::whereDate('date', '>=', now()->subDays(29)->toDateString())
            ->selectRaw('date, SUM(total) total, SUM(total - cost_total) profit')->groupBy('date')->get()->keyBy(fn ($r) => $r->date);
        $chart = collect(range(29, 0))->map(function ($d) use ($daily) {
            $day = now()->subDays($d)->toDateString();
            $row = $daily->first(fn ($r, $k) => str_starts_with($k, $day));

            return ['d' => now()->subDays($d)->format('d M'), 'sales' => round((float) ($row->total ?? 0)), 'profit' => round((float) ($row->profit ?? 0))];
        });

        $topProducts = DB::table('sales_lines')
            ->join('invoices', fn ($j) => $j->on('invoices.id', '=', 'sales_lines.document_id')->where('sales_lines.document_type', (new Invoice)->getMorphClass()))
            ->join('products', 'products.id', '=', 'sales_lines.product_id')
            ->whereDate('invoices.date', '>=', now()->subDays(30)->toDateString())
            ->selectRaw('products.id, products.code, products.name, products.colour, SUM(sales_lines.qty) pairs, SUM(sales_lines.line_total) sales')
            ->groupBy('products.id', 'products.code', 'products.name', 'products.colour')->orderByDesc('sales')->limit(5)->get();

        return view('dashboard', [
            'todaySales' => $todaySales,
            'month' => $month,
            'monthExpenses' => $monthExpenses,
            'receivable' => $receivable,
            'overdue' => $overdue,
            'payable' => $payable,
            'stockValue' => $stockValue,
            'stockPairs' => $stockPairs,
            'accounts' => $accounts,
            'chart' => $chart,
            'topProducts' => $topProducts,
            'alerts' => Alerts::forTopbar(),
            'overdueCustomers' => Customer::whereIn('id', $overdue->pluck('customer_id')->unique())->get()
                ->map(fn ($c) => ['customer' => $c, 'amount' => $overdue->where('customer_id', $c->id)->sum(fn ($i) => $i->balance()), 'days' => $overdue->where('customer_id', $c->id)->max(fn ($i) => $i->daysOverdue())])
                ->sortByDesc('amount')->take(5),
            'chequesDue' => Cheque::whereIn('status', ['pending', 'deposited'])->whereDate('cheque_date', '<=', now()->addDays(14))->orderBy('cheque_date')->limit(6)->get(),
            'inTransit' => PurchaseOrder::with('supplier')->whereIn('status', ['ordered', 'costed', 'partial'])->orderBy('expected_date')->limit(5)->get(),
            'recent' => Invoice::with('customer')->latest('date')->latest('id')->limit(6)->get(),
            'lowStock' => Product::where('is_active', true)->withSum('stocks as on_hand', 'qty')->get()->filter(fn ($p) => (int) $p->on_hand <= $p->reorder_level)->sortBy('on_hand')->take(5),
        ]);
    }
}
