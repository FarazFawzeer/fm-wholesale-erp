<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\Category;
use App\Models\Cheque;
use App\Models\CreditNote;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    protected function range(Request $request, int $days = 30): array
    {
        return [
            $request->input('from', now()->subDays($days)->toDateString()),
            $request->input('to', now()->toDateString()),
        ];
    }

    public function stockValuation(Request $request)
    {
        $wh = $request->warehouse_id;
        $products = Product::with(['brand', 'category'])
            ->withSum(['stocks as on_hand' => fn ($q) => $q->when($wh, fn ($w) => $w->where('warehouse_id', $wh))], 'qty')
            ->withSum(['stocks as damaged' => fn ($q) => $q->when($wh, fn ($w) => $w->where('warehouse_id', $wh))], 'damaged_qty')
            ->orderBy('code')->get()->filter(fn ($p) => $p->on_hand > 0 || $p->damaged > 0);

        return view('reports.stock-valuation', [
            'products' => $products,
            'byCategory' => $products->groupBy(fn ($p) => $p->category?->name ?? 'Uncategorised'),
            'warehouses' => Warehouse::all(),
        ]);
    }

    public function lowStock()
    {
        $products = Product::with(['brand', 'variants.stocks'])->where('is_active', true)->withSum('stocks as on_hand', 'qty')->get()
            ->map(function ($p) {
                $p->setAttribute('empty_sizes', $p->variants->filter(fn ($v) => $v->stocks->sum('qty') <= 0)->pluck('size'));

                return $p;
            })
            ->filter(fn ($p) => $p->on_hand <= $p->reorder_level || $p->empty_sizes->count())
            ->sortBy('on_hand');

        return view('reports.low-stock', compact('products'));
    }

    public function statement(Request $request)
    {
        [$from, $to] = $this->range($request, 90);
        $customer = $request->customer_id ? Customer::find($request->customer_id) : null;
        $rows = collect();
        $opening = 0;

        if ($customer) {
            $before = fn ($q) => $q->where('customer_id', $customer->id)->whereDate('date', '<', $from);
            $opening = $customer->opening_balance
                + Invoice::where($before)->sum('total')
                - CreditNote::where($before)->sum('total')
                - Payment::where('party_type', 'customer')->where('party_id', $customer->id)->whereDate('date', '<', $from)->sum('total');

            $in = fn ($q) => $q->whereDate('date', '>=', $from)->whereDate('date', '<=', $to);
            $rows = collect()
                ->merge(Invoice::where('customer_id', $customer->id)->where($in)->get()->map(fn ($i) => ['date' => $i->date, 'ref' => $i->number, 'desc' => 'Invoice' . ($i->sale_type === 'cash' ? ' (cash)' : '') . ' - due ' . $i->due_date->format('d M'), 'debit' => $i->total, 'credit' => 0, 'url' => route('invoices.show', $i)]))
                ->merge(CreditNote::where('customer_id', $customer->id)->where($in)->get()->map(fn ($c) => ['date' => $c->date, 'ref' => $c->number, 'desc' => 'Credit note - ' . $c->reason, 'debit' => 0, 'credit' => $c->total, 'url' => route('credit-notes.show', $c)]))
                ->merge(Payment::with('lines')->where('party_type', 'customer')->where('party_id', $customer->id)->where($in)->get()->map(fn ($p) => ['date' => $p->date, 'ref' => $p->number, 'desc' => 'Payment - ' . $p->methodSummary(), 'debit' => 0, 'credit' => $p->total, 'url' => route('payments.show', $p)]))
                ->sortBy(fn ($r) => $r['date']->format('Ymd') . ($r['debit'] > 0 ? '0' : '1'))->values();
        }

        return view('reports.statement', [
            'customers' => Customer::orderBy('name')->get(),
            'customer' => $customer,
            'rows' => $rows,
            'opening' => $opening,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function outstanding()
    {
        $buckets = ['current' => 'Not due', 'd30' => '1-30 days', 'd60' => '31-60 days', 'd90' => '61-90 days', 'd90p' => '90+ days'];
        $rows = Invoice::with('customer')->open()->get()->groupBy('customer_id')->map(function ($invoices) use ($buckets) {
            $row = array_fill_keys(array_keys($buckets), 0) + ['customer' => $invoices->first()->customer, 'total' => 0];
            foreach ($invoices as $inv) {
                $days = $inv->daysOverdue();
                $key = $days <= 0 ? 'current' : ($days <= 30 ? 'd30' : ($days <= 60 ? 'd60' : ($days <= 90 ? 'd90' : 'd90p')));
                $row[$key] += $inv->balance();
                $row['total'] += $inv->balance();
            }

            return $row;
        })->sortByDesc('total');

        return view('reports.outstanding', compact('rows', 'buckets'));
    }

    /** Sales, cost and profit per product, net of returns. */
    public function productProfit(Request $request)
    {
        [$from, $to] = $this->range($request);
        $rows = DB::table('sales_lines')
            ->join('invoices', fn ($j) => $j->on('invoices.id', '=', 'sales_lines.document_id')->where('sales_lines.document_type', (new Invoice)->getMorphClass()))
            ->join('products', 'products.id', '=', 'sales_lines.product_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->whereDate('invoices.date', '>=', $from)->whereDate('invoices.date', '<=', $to)
            ->when($request->category_id, fn ($q, $c) => $q->where('products.category_id', $c))
            ->selectRaw('products.id, products.code, products.name, products.colour, brands.name as brand,
                SUM(sales_lines.qty - sales_lines.returned_qty) pairs,
                SUM(sales_lines.line_total * (sales_lines.qty - sales_lines.returned_qty) / sales_lines.qty) sales,
                SUM(sales_lines.unit_cost * (sales_lines.qty - sales_lines.returned_qty)) cost')
            ->groupBy('products.id', 'products.code', 'products.name', 'products.colour', 'brands.name')
            ->orderByDesc('sales')->get()
            ->map(function ($r) {
                $r->profit = $r->sales - $r->cost;
                $r->margin = $r->sales > 0 ? $r->profit / $r->sales * 100 : 0;

                return $r;
            });

        return view('reports.product-profit', ['rows' => $rows, 'from' => $from, 'to' => $to, 'categories' => Category::orderBy('name')->get()]);
    }

    public function invoiceProfit(Request $request)
    {
        [$from, $to] = $this->range($request);
        $invoices = Invoice::with('customer')->whereDate('date', '>=', $from)->whereDate('date', '<=', $to)->latest('date')->get()
            ->map(function ($i) {
                $net = $i->total - $i->credited_amount;
                $i->setAttribute('net', $net);
                $i->setAttribute('gp', $net - $i->cost_total);

                return $i;
            });
        $expenses = Expense::whereDate('date', '>=', $from)->whereDate('date', '<=', $to)->sum('amount');

        return view('reports.invoice-profit', compact('invoices', 'from', 'to', 'expenses'));
    }

    public function cashFlow(Request $request)
    {
        [$from, $to] = $this->range($request);
        $tx = AccountTransaction::whereDate('date', '>=', $from)->whereDate('date', '<=', $to)->where('type', '!=', 'transfer')->get();
        $labels = ['receipt' => 'Customer receipts', 'cheque_cleared' => 'Customer cheques cleared', 'payment' => 'Supplier payments', 'cheque_paid' => 'Issued cheques cleared', 'expense' => 'Expenses'];

        $byType = $tx->groupBy('type')->map(fn ($g, $t) => ['label' => $labels[$t] ?? ucfirst($t), 'in' => $g->where('amount', '>', 0)->sum('amount'), 'out' => -$g->where('amount', '<', 0)->sum('amount')]);
        $byWeek = $tx->groupBy(fn ($t) => $t->date->copy()->startOfWeek()->format('Y-m-d'))->sortKeys()
            ->map(fn ($g, $w) => ['week' => \Carbon\Carbon::parse($w)->format('d M'), 'in' => $g->where('amount', '>', 0)->sum('amount'), 'out' => -$g->where('amount', '<', 0)->sum('amount')]);
        $expenses = Expense::with('category')->whereDate('date', '>=', $from)->whereDate('date', '<=', $to)->get()->groupBy(fn ($e) => $e->category->name)->map->sum('amount')->sortDesc();

        return view('reports.cash-flow', compact('byType', 'byWeek', 'expenses', 'from', 'to'));
    }

    /** Expected incoming (customer dues + cheques to clear) and outgoing (supplier dues + issued cheques). */
    public function expected(Request $request)
    {
        $days = (int) $request->input('days', 30);
        $until = now()->addDays($days)->endOfDay();
        $items = collect();

        foreach (Invoice::with('customer')->open()->whereDate('due_date', '<=', $until)->get() as $i) {
            $items->push(['date' => $i->due_date, 'dir' => 'in', 'what' => 'Invoice ' . $i->number, 'party' => $i->customer->name, 'amount' => $i->balance(), 'overdue' => $i->due_date->isPast(), 'url' => route('invoices.show', $i)]);
        }
        foreach (Cheque::whereIn('status', ['pending', 'deposited'])->whereDate('cheque_date', '<=', $until)->get() as $c) {
            $items->push(['date' => $c->cheque_date, 'dir' => $c->direction === 'received' ? 'in' : 'out', 'what' => 'Cheque #' . $c->cheque_no . ' (' . $c->bank . ')', 'party' => $c->party()?->name, 'amount' => $c->amount, 'overdue' => $c->cheque_date->isPast(), 'url' => route('cheques.index', ['direction' => $c->direction])]);
        }
        foreach (PurchaseOrder::with('supplier')->whereRaw('payable_amount - paid_amount > 0.009')->whereDate('due_date', '<=', $until)->get() as $po) {
            $items->push(['date' => $po->due_date, 'dir' => 'out', 'what' => 'Supplier balance ' . $po->number, 'party' => $po->supplier->name, 'amount' => $po->balance(), 'overdue' => $po->due_date->isPast(), 'url' => route('purchase-orders.show', $po)]);
        }
        $items = $items->sortBy('date')->values();

        $weeks = $items->groupBy(fn ($i) => $i['date']->lt(now()->startOfDay()) ? '0000' : $i['date']->copy()->startOfWeek()->format('Y-m-d'))->sortKeys()
            ->map(fn ($g, $k) => ['label' => $k === '0000' ? 'Overdue / past due' : 'Week of ' . \Carbon\Carbon::parse($k)->format('d M'), 'in' => $g->where('dir', 'in')->sum('amount'), 'out' => $g->where('dir', 'out')->sum('amount')]);

        $cash = \App\Models\Account::where('is_active', true)->get()->sum(fn ($a) => $a->balance());

        return view('reports.expected', compact('items', 'weeks', 'days', 'cash'));
    }

    public function importCosting()
    {
        $orders = PurchaseOrder::with(['supplier', 'charges'])->withSum('items as pairs', 'qty')->where('landed_total', '>', 0)->latest('order_date')->get();

        return view('reports.import-costing', compact('orders'));
    }
}
