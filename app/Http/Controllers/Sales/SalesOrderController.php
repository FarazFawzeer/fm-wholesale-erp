<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Services\SalesService;
use App\Support\Catalog;
use Illuminate\Http\Request;

/** Quotations and sales orders (same screens, different type). */
class SalesOrderController extends Controller
{
    public function index(Request $request, string $type)
    {
        $docs = SalesOrder::with('customer')->where('type', $type)
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('number', 'like', "%$s%")->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%$s%"))))
            ->latest('date')->latest('id')->paginate(25)->withQueryString();

        return view('sales.orders.index', ['docs' => $docs, 'type' => $type]);
    }

    public function create(Request $request, string $type)
    {
        return view('sales.orders.form', $this->formData(new SalesOrder([
            'type' => $type,
            'date' => now(),
            'valid_until' => $type === 'quotation' ? now()->addDays(14) : null,
            'customer_id' => $request->customer_id,
        ])));
    }

    public function store(Request $request)
    {
        $doc = SalesService::saveSalesOrder(null, $this->validated($request), $request->input('lines', []));

        return redirect()->route('sales-docs.show', $doc)->with('success', "{$doc->label()} {$doc->number} saved.");
    }

    public function show(SalesOrder $salesOrder)
    {
        $salesOrder->load(['customer.group', 'lines.product', 'lines.variant', 'invoices', 'convertedFrom', 'user']);

        return view('sales.orders.show', ['doc' => $salesOrder, 'byProduct' => $salesOrder->lines->groupBy('product_id')]);
    }

    public function edit(SalesOrder $salesOrder)
    {
        abort_unless($salesOrder->status === 'open', 403, 'Only open documents can be edited.');

        return view('sales.orders.form', $this->formData($salesOrder->load('lines')));
    }

    public function update(Request $request, SalesOrder $salesOrder)
    {
        abort_unless($salesOrder->status === 'open', 403);
        SalesService::saveSalesOrder($salesOrder, $this->validated($request), $request->input('lines', []));

        return redirect()->route('sales-docs.show', $salesOrder)->with('success', 'Saved.');
    }

    public function status(Request $request, SalesOrder $salesOrder)
    {
        $request->validate(['status' => 'required|in:accepted,rejected,open']);
        $salesOrder->update(['status' => $request->status]);

        return back()->with('success', 'Status updated.');
    }

    /** Quotation -> sales order, or order/quotation -> invoice (opens the invoice form pre-filled). */
    public function convert(Request $request, SalesOrder $salesOrder)
    {
        if ($request->to === 'invoice') {
            return redirect()->route('invoices.create', ['from' => $salesOrder->id]);
        }
        $order = SalesService::saveSalesOrder(null, [
            'type' => 'order',
            'customer_id' => $salesOrder->customer_id,
            'date' => now()->toDateString(),
            'notes' => $salesOrder->notes,
        ], SalesService::toGrid($salesOrder->load('lines')));
        $order->update(['converted_from_id' => $salesOrder->id]);
        $salesOrder->update(['status' => 'converted']);

        return redirect()->route('sales-docs.show', $order)->with('success', "Quotation converted to sales order {$order->number}.");
    }

    protected function formData(SalesOrder $doc): array
    {
        return [
            'doc' => $doc,
            'customers' => InvoiceController::customerOptions(),
            'catalog' => Catalog::forEditor(),
            'initial' => old('lines') ? array_values(old('lines')) : ($doc->exists ? SalesService::toGrid($doc) : []),
        ];
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'type' => 'required|in:quotation,order',
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'valid_until' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);
    }
}
