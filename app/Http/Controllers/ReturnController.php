<?php

namespace App\Http\Controllers;

use App\Models\GoodsReturn;
use App\Models\Invoice;
use App\Models\Purchase;
use App\Services\PaymentService;
use App\Services\TradeService;
use Illuminate\Http\Request;

/** Goods back from customers and goods sent back to suppliers - one place for both. */
class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $type = in_array($request->type, ['customer', 'supplier']) ? $request->type : null;

        $query = GoodsReturn::query()
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('number', 'like', "%$s%")->orWhere('reason', 'like', "%$s%")
                ->orWhereHas('invoice', fn ($i) => $i->where('number', 'like', "%$s%")->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%$s%")))
                ->orWhereHas('purchase', fn ($p) => $p->where('number', 'like', "%$s%")->orWhereHas('supplier', fn ($c) => $c->where('name', 'like', "%$s%")))))
            ->when($request->from, fn ($q, $d) => $q->whereDate('date', '>=', $d))
            ->when($request->to, fn ($q, $d) => $q->whereDate('date', '<=', $d));

        return view('returns.index', [
            'returns' => (clone $query)->with(['invoice.customer', 'purchase.supplier'])->withSum('items as pairs', 'qty')
                ->latest('date')->latest('id')->paginate(30)->withQueryString(),
            'type' => $type,
            'fromCustomers' => $this->totals((clone $query)->where('type', 'customer')),
            'toSuppliers' => $this->totals((clone $query)->where('type', 'supplier')),
        ]);
    }

    protected function totals($query): object
    {
        $ids = $query->pluck('id');

        return (object) [
            'count' => $ids->count(),
            'amount' => (float) GoodsReturn::whereIn('id', $ids)->sum('total'),
            'pairs' => (int) \App\Models\ReturnItem::whereIn('return_id', $ids)->sum('qty'),
        ];
    }

    /** Start a return: pick the sale or the purchase. */
    public function create(Request $request)
    {
        return view('returns.start', [
            'invoices' => Invoice::with('customer')->latest('date')->latest('id')->limit(300)->get(),
            'purchases' => Purchase::with('supplier')->latest('date')->latest('id')->limit(300)->get(),
            'tab' => $request->type === 'supplier' ? 'supplier' : 'customer',
        ]);
    }

    public function pick(Request $request)
    {
        if ($request->filled('invoice_id')) {
            return redirect()->route('returns.customer', $request->integer('invoice_id'));
        }
        if ($request->filled('purchase_id')) {
            return redirect()->route('returns.supplier', $request->integer('purchase_id'));
        }

        return back()->withErrors(['pick' => 'Choose the bill first.']);
    }

    public function customerForm(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product', 'items.colour', 'returns']);

        return view('returns.form', ['doc' => $invoice, 'type' => 'customer']);
    }

    public function customerStore(Request $request, Invoice $invoice)
    {
        $data = $request->validate(['date' => 'required|date', 'reason' => 'nullable|max:255', 'qty' => 'array']);
        $invoice->load('items');
        $return = TradeService::customerReturn($invoice, $request->input('qty', []), $data);

        return redirect()->route('returns.show', $return)
            ->with('success', 'Return saved. ' . $return->items()->sum('qty') . ' pairs are back in stock and ' . money($return->total) . ' is taken off the bill.');
    }

    public function supplierForm(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product', 'items.colour', 'returns']);

        return view('returns.form', ['doc' => $purchase, 'type' => 'supplier']);
    }

    public function supplierStore(Request $request, Purchase $purchase)
    {
        $data = $request->validate(['date' => 'required|date', 'reason' => 'nullable|max:255', 'qty' => 'array']);
        $purchase->load('items');
        $return = TradeService::supplierReturn($purchase, $request->input('qty', []), $data);

        return redirect()->route('returns.show', $return)
            ->with('success', 'Return saved. ' . $return->items()->sum('qty') . ' pairs taken out of stock and ' . money($return->total) . ' is taken off what you owe.');
    }

    public function show(GoodsReturn $return)
    {
        return view('returns.show', $this->detail($return));
    }

    public function print(GoodsReturn $return)
    {
        return view('returns.print', $this->detail($return));
    }

    protected function detail(GoodsReturn $return): array
    {
        $return->load(['items.colour.product', 'invoice.customer', 'purchase.supplier', 'user']);
        $isCustomer = $return->type === 'customer';
        $doc = $isCustomer ? $return->invoice : $return->purchase;
        $party = $isCustomer ? $doc->customer : $doc->supplier;

        return [
            'return' => $return,
            'isCustomer' => $isCustomer,
            'doc' => $doc,
            'party' => $party,
            'groups' => $return->items->groupBy(fn ($it) => $it->colour->product_id),
            'partyBalance' => $party->balance(),
            'credit' => PaymentService::unusedCredit($isCustomer ? 'customer' : 'supplier', $party->id),
        ];
    }
}
