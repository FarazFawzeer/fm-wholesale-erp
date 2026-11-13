<?php

namespace App\Http\Controllers;

use App\Models\Cheque;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $sales = fn ($from, $to) => Invoice::whereBetween('date', [$from, $to])
            ->selectRaw('COUNT(*) AS bills, COALESCE(SUM(total - returned_amount), 0) AS amount, COALESCE(SUM(total - returned_amount - cost_total), 0) AS profit')->first();

        $todaySales = $sales($today, $today);
        $yesterday = $sales(now()->subDay()->toDateString(), now()->subDay()->toDateString());
        $month = $sales(now()->startOfMonth()->toDateString(), $today);
        $lastMonth = $sales(now()->subMonthNoOverflow()->startOfMonth()->toDateString(), now()->subMonthNoOverflow()->toDateString());

        // Unpaid reminder: customers who still owe, oldest first (no time limit)
        $owing = Customer::withDue()->get()->filter(fn ($c) => $c->due > 0.009);
        $unpaid = $owing->map(function ($c) {
            $oldest = Invoice::where('customer_id', $c->id)->unpaid()->min('date');
            $since = $oldest ? \Carbon\Carbon::parse($oldest) : $c->created_at;

            return ['customer' => $c, 'due' => (float) $c->due, 'days' => (int) $since->diffInDays(now()->startOfDay())];
        })->sortByDesc('days')->values();

        $products = Product::with('colours')->where('is_active', true)->get();

        // Last 30 days, every day present
        $daily = Invoice::where('date', '>=', now()->subDays(29)->toDateString())
            ->selectRaw('date, SUM(total - returned_amount) AS amount, SUM(total - returned_amount - cost_total) AS profit')
            ->groupBy('date')->get()->keyBy(fn ($r) => \Carbon\Carbon::parse($r->date)->toDateString());
        $chart = collect(range(29, 0))->map(function ($i) use ($daily) {
            $d = now()->subDays($i);
            $row = $daily[$d->toDateString()] ?? null;

            return ['d' => $d->format('d M'), 'sales' => round((float) ($row->amount ?? 0)), 'profit' => round((float) ($row->profit ?? 0))];
        })->values();

        $methods = DB::table('payment_lines')
            ->join('payments', 'payments.id', '=', 'payment_lines.payment_id')
            ->leftJoin('cheques', 'cheques.id', '=', 'payment_lines.cheque_id')
            ->where('payments.direction', 'in')
            ->where('payments.date', '>=', now()->startOfMonth()->toDateString())
            ->where(fn ($q) => $q->whereNull('cheques.id')->orWhere('cheques.status', '!=', 'bounced'))
            ->groupBy('payment_lines.method')
            ->selectRaw('payment_lines.method AS method, SUM(payment_lines.amount) AS total')
            ->pluck('total', 'method');

        $top = DB::table('invoice_items')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('products', 'products.id', '=', 'invoice_items.product_id')
            ->where('invoices.date', '>=', now()->subDays(29)->toDateString())
            ->groupBy('products.id', 'products.name', 'products.code', 'products.image_path')
            ->selectRaw('products.id, products.name, products.code, products.image_path, SUM(invoice_items.qty - invoice_items.returned_qty) AS pairs, SUM((invoice_items.qty - invoice_items.returned_qty) * invoice_items.unit_price) AS amount')
            ->orderByDesc('pairs')->limit(5)->get();

        return view('dashboard', [
            'todaySales' => $todaySales,
            'todayTrend' => $this->trend($todaySales->amount, $yesterday->amount),
            'todayIn' => (float) Payment::where('direction', 'in')->whereDate('date', $today)->sum('total'),
            'month' => $month,
            'monthTrend' => $this->trend($month->amount, $lastMonth->amount),
            'customerDue' => $owing->sum('due'),
            'supplierDue' => Supplier::withDue()->get()->sum(fn ($s) => max(0, $s->due)),
            'unpaid' => $unpaid,
            'lowStock' => $products->filter->isLow()->sortBy(fn ($p) => $p->totalStock())->take(6)->values(),
            'stockPairs' => $products->sum(fn ($p) => $p->totalStock()),
            'stockValue' => $products->sum(fn ($p) => max(0, $p->totalStock()) * $p->cost_price),
            'recent' => Invoice::with('customer')->latest('date')->latest('id')->limit(7)->get(),
            'chequesDue' => Cheque::where('status', 'pending')->whereDate('cheque_date', '<=', now()->addDays(3))->orderBy('cheque_date')->get(),
            'chart' => $chart,
            'chart30' => ['sales' => $chart->sum('sales'), 'profit' => $chart->sum('profit')],
            'methods' => collect(Payment::METHODS)->map(fn ($label, $key) => ['label' => $label, 'value' => round((float) ($methods[$key] ?? 0))])->values(),
            'top' => $top,
        ]);
    }

    /** Percent change, null when there is nothing to compare with. */
    protected function trend($now, $before): ?float
    {
        return $before > 0 ? round(($now - $before) / $before * 100) : null;
    }
}
