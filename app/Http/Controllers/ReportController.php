<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    protected function range(Request $request): array
    {
        $preset = $request->period;
        [$from, $to] = match ($preset) {
            'today' => [now()->toDateString(), now()->toDateString()],
            'week' => [now()->startOfWeek()->toDateString(), now()->toDateString()],
            'last_month' => [now()->subMonthNoOverflow()->startOfMonth()->toDateString(), now()->subMonthNoOverflow()->endOfMonth()->toDateString()],
            'year' => [now()->startOfYear()->toDateString(), now()->toDateString()],
            default => [$request->from ?? now()->startOfMonth()->toDateString(), $request->to ?? now()->toDateString()],
        };

        return [$from, $to];
    }

    /** Money summary: sales, profit, expenses, money in / out by method. */
    public function index(Request $request)
    {
        [$from, $to] = $this->range($request);

        $sales = Invoice::whereBetween('date', [$from, $to])
            ->selectRaw('COUNT(*) AS bills, COALESCE(SUM(total - returned_amount), 0) AS amount, COALESCE(SUM(cost_total), 0) AS cost, COALESCE(SUM(discount), 0) AS discount')
            ->first();
        $pairs = (int) DB::table('invoice_items')->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->whereBetween('invoices.date', [$from, $to])->sum(DB::raw('invoice_items.qty - invoice_items.returned_qty'));
        $expenses = (float) Expense::whereBetween('date', [$from, $to])->sum('amount');
        $bought = Purchase::whereBetween('date', [$from, $to])->selectRaw('COUNT(*) AS bills, COALESCE(SUM(total - returned_amount), 0) AS amount')->first();

        $byMethod = fn (string $direction) => DB::table('payment_lines')
            ->join('payments', 'payments.id', '=', 'payment_lines.payment_id')
            ->leftJoin('cheques', 'cheques.id', '=', 'payment_lines.cheque_id')
            ->where('payments.direction', $direction)
            ->whereBetween('payments.date', [$from, $to])
            ->where(fn ($q) => $q->whereNull('cheques.id')->orWhere('cheques.status', '!=', 'bounced'))
            ->groupBy('payment_lines.method')
            ->selectRaw('payment_lines.method AS method, SUM(payment_lines.amount) AS total')
            ->pluck('total', 'method')->map(fn ($v) => (float) $v);

        return view('reports.index', [
            'from' => $from,
            'to' => $to,
            'sales' => $sales,
            'pairs' => $pairs,
            'gross' => $sales->amount - $sales->cost,
            'expenses' => $expenses,
            'expensesByType' => Expense::with('category')->whereBetween('date', [$from, $to])->selectRaw('expense_category_id, SUM(amount) AS total')
                ->groupBy('expense_category_id')->get()->sortByDesc('total'),
            'expensesByMethod' => Expense::whereBetween('date', [$from, $to])->groupBy('method')->selectRaw('method, SUM(amount) AS total')->pluck('total', 'method'),
            'bought' => $bought,
            'moneyIn' => $byMethod('in'),
            'moneyOut' => $byMethod('out'),
            'customerDue' => Customer::withDue()->get()->sum(fn ($c) => max(0, $c->due)),
            'supplierDue' => Supplier::withDue()->get()->sum(fn ($s) => max(0, $s->due)),
            'daily' => Invoice::whereBetween('date', [$from, $to])->selectRaw('date, SUM(total - returned_amount) AS amount, SUM(total - returned_amount - cost_total) AS profit')
                ->groupBy('date')->orderBy('date')->get(),
        ]);
    }

    /** Best sellers: pairs and profit per product. */
    public function products(Request $request)
    {
        [$from, $to] = $this->range($request);
        $rows = DB::table('invoice_items')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('products', 'products.id', '=', 'invoice_items.product_id')
            ->whereBetween('invoices.date', [$from, $to])
            ->groupBy('products.id', 'products.name', 'products.code', 'products.image_path')
            ->selectRaw('products.id, products.name, products.code, products.image_path,
                SUM(invoice_items.qty - invoice_items.returned_qty) AS pairs,
                SUM((invoice_items.qty - invoice_items.returned_qty) * invoice_items.unit_price) AS amount,
                SUM((invoice_items.qty - invoice_items.returned_qty) * (invoice_items.unit_price - invoice_items.unit_cost)) AS profit')
            ->orderByDesc('pairs')
            ->get();

        $colours = DB::table('invoice_items')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->join('product_colours', 'product_colours.id', '=', 'invoice_items.product_colour_id')
            ->whereBetween('invoices.date', [$from, $to])
            ->groupBy('product_colours.colour')
            ->selectRaw('product_colours.colour, SUM(invoice_items.qty - invoice_items.returned_qty) AS pairs')
            ->orderByDesc('pairs')->limit(12)->get();

        return view('reports.products', compact('from', 'to', 'rows', 'colours'));
    }

    /**
     * Stock on hand with value and AGE: how long the pairs still in stock have been here.
     * "In stock since" = the date the oldest pairs still on hand came in (first in, first out).
     */
    public function stock(Request $request)
    {
        $products = Product::with(['colours', 'brand'])->where('is_active', true)->orderBy('name')->get();

        // pairs that came in, newest first, per product
        $incoming = DB::table('stock_movements')
            ->join('product_colours', 'product_colours.id', '=', 'stock_movements.product_colour_id')
            ->where('stock_movements.qty', '>', 0)
            ->whereIn('stock_movements.type', ['opening', 'purchase', 'return', 'correction'])
            ->orderByDesc('stock_movements.date')->orderByDesc('stock_movements.id')
            ->get(['product_colours.product_id', 'stock_movements.date', 'stock_movements.qty'])
            ->groupBy('product_id');
        $lastSold = DB::table('invoice_items')->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->groupBy('invoice_items.product_id')->selectRaw('invoice_items.product_id, MAX(invoices.date) AS d')->pluck('d', 'product_id');

        $today = now()->startOfDay();
        foreach ($products as $p) {
            $stock = $p->totalStock();
            $since = null;
            if ($stock > 0) {
                $acc = 0;
                foreach ($incoming[$p->id] ?? [] as $m) {
                    $acc += $m->qty;
                    $since = $m->date;
                    if ($acc >= $stock) {
                        break;
                    }
                }
                $since = \Illuminate\Support\Carbon::parse($since ?? $p->created_at)->startOfDay();
            }
            $p->stock_since = $since;
            $p->age_days = $since ? (int) $since->diffInDays($today) : null;
            $p->last_sold = isset($lastSold[$p->id]) ? \Illuminate\Support\Carbon::parse($lastSold[$p->id]) : null;
        }

        $age = (int) $request->age; // months
        $products = $products
            ->when($request->show === 'low', fn ($c) => $c->filter->isLow())
            ->when($age > 0, fn ($c) => $c->filter(fn ($p) => $p->stock_since && $p->stock_since->lte($today->copy()->subMonthsNoOverflow($age))))
            ->when($request->from, fn ($c, $d) => $c->filter(fn ($p) => $p->stock_since && $p->stock_since->gte(\Illuminate\Support\Carbon::parse($d))))
            ->when($request->to, fn ($c, $d) => $c->filter(fn ($p) => $p->stock_since && $p->stock_since->lte(\Illuminate\Support\Carbon::parse($d))))
            ->when($request->sort === 'oldest', fn ($c) => $c->sortByDesc(fn ($p) => $p->age_days ?? -1))
            ->when($request->sort === 'pairs', fn ($c) => $c->sortByDesc(fn ($p) => $p->totalStock()))
            ->values();

        $old = $products->filter(fn ($p) => $p->age_days !== null && $p->age_days >= 182);

        return view('reports.stock', [
            'products' => $products,
            'pairs' => $products->sum(fn ($p) => max(0, $p->totalStock())),
            'costValue' => $products->sum(fn ($p) => max(0, $p->totalStock()) * $p->cost_price),
            'saleValue' => $products->sum(fn ($p) => max(0, $p->totalStock()) * $p->selling_price),
            'oldPairs' => $old->sum(fn ($p) => $p->totalStock()),
            'oldValue' => $old->sum(fn ($p) => $p->totalStock() * $p->cost_price),
        ]);
    }

    /** Pairs paid for but not received (damaged, customs, missing) - from purchases in the period. */
    public function missing(Request $request)
    {
        [$from, $to] = $this->range($request);
        $rows = \App\Models\PurchaseItem::with(['purchase.supplier', 'product', 'colour'])
            ->where('missing_qty', '>', 0)
            ->whereHas('purchase', fn ($q) => $q->whereBetween('date', [$from, $to]))
            ->get()
            ->sortByDesc(fn ($it) => $it->purchase->date->format('Ymd') . str_pad($it->id, 8, '0', STR_PAD_LEFT))->values();

        return view('reports.missing', [
            'from' => $from,
            'to' => $to,
            'rows' => $rows,
            'pairs' => $rows->sum('missing_qty'),
            'value' => $rows->sum(fn ($it) => $it->missing_qty * $it->unit_cost),
            'billed' => (int) \App\Models\PurchaseItem::whereHas('purchase', fn ($q) => $q->whereBetween('date', [$from, $to]))->sum('qty'),
            'byReason' => $rows->groupBy(fn ($it) => $it->missing_reason ?: 'Missing')->map(fn ($g) => ['pairs' => $g->sum('missing_qty'), 'value' => $g->sum(fn ($it) => $it->missing_qty * $it->unit_cost)])->sortByDesc('pairs'),
            'bySupplier' => $rows->groupBy(fn ($it) => $it->purchase->supplier->name)->map(fn ($g) => $g->sum('missing_qty'))->sortDesc(),
        ]);
    }

    /** Statement of one customer: every bill, payment and return in order with running balance. */
    public function statement(Customer $customer)
    {
        $rows = collect();
        foreach ($customer->invoices()->with('returns')->get() as $inv) {
            $rows->push(['date' => $inv->date, 'text' => 'Invoice ' . $inv->number, 'url' => route('invoices.show', $inv), 'debit' => $inv->total, 'credit' => 0]);
            foreach ($inv->returns as $r) {
                $rows->push(['date' => $r->date, 'text' => 'Return ' . $r->number, 'url' => route('invoices.show', $inv), 'debit' => 0, 'credit' => $r->total]);
            }
        }
        foreach (Payment::with('lines')->where('party_type', 'customer')->where('party_id', $customer->id)->get() as $p) {
            $rows->push(['date' => $p->date, 'text' => 'Payment ' . $p->number . ' (' . $p->methodSummary() . ')', 'url' => route('payments.show', $p), 'debit' => 0, 'credit' => $p->total]);
        }
        $rows = $rows->sortBy(fn ($r) => $r['date']->format('Ymd'))->values();

        return view('reports.statement', ['customer' => $customer, 'rows' => $rows]);
    }
}
