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

    /** Stock on hand with value. */
    public function stock(Request $request)
    {
        $products = Product::with(['colours', 'brand'])->where('is_active', true)->orderBy('name')->get()
            ->when($request->show === 'low', fn ($c) => $c->filter->isLow()->values());

        return view('reports.stock', [
            'products' => $products,
            'pairs' => $products->sum(fn ($p) => $p->totalStock()),
            'costValue' => $products->sum(fn ($p) => max(0, $p->totalStock()) * $p->cost_price),
            'saleValue' => $products->sum(fn ($p) => max(0, $p->totalStock()) * $p->selling_price),
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
