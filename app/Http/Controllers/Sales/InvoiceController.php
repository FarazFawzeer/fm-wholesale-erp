<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\SalesOrder;
use App\Models\Warehouse;
use App\Services\SalesService;
use App\Support\Catalog;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::with('customer')
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('number', 'like', "%$s%")->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%$s%"))))
            ->when($request->status === 'overdue', fn ($q) => $q->overdue())
            ->when(in_array($request->status, ['unpaid', 'partial', 'paid']), fn ($q) => $q->where('payment_status', $request->status))
            ->when($request->delivery, fn ($q, $d) => $q->where('delivery_status', $d))
            ->when($request->type, fn ($q, $t) => $q->where('sale_type', $t))
            ->when($request->from, fn ($q, $d) => $q->whereDate('date', '>=', $d))
            ->when($request->to, fn ($q, $d) => $q->whereDate('date', '<=', $d))
            ->latest('date')->latest('id');

        $totals = (clone $invoices)->reorder()->selectRaw('SUM(total) total, SUM(total - paid_amount - credited_amount) balance, SUM(total - cost_total) profit')->first();

        return view('sales.invoices.index', ['invoices' => $invoices->paginate(25)->withQueryString(), 'totals' => $totals]);
    }

    public function create(Request $request)
    {
        $initial = [];
        $order = null;
        if ($request->from) {
            $order = SalesOrder::with('lines')->findOrFail($request->from);
            $initial = SalesService::toGrid($order);
        }

        return view('sales.invoices.create', [
            'customers' => $this->customerOptions(),
            'warehouses' => Warehouse::orderBy('id')->get(),
            'accounts' => Account::where('is_active', true)->get(['id', 'name', 'type']),
            'catalog' => Catalog::forEditor(),
            'initial' => old('lines') ? array_values(old('lines')) : $initial,
            'order' => $order,
            'customerId' => old('customer_id', $order?->customer_id ?? $request->customer_id),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'date' => 'required|date',
            'sale_type' => 'required|in:cash,credit',
            'sales_order_id' => 'nullable|exists:sales_orders,id',
            'notes' => 'nullable|string',
            'lines' => 'required|array',
        ]);
        $data['override_credit'] = $request->boolean('override_credit');
        $payments = $request->input('payments', []);
        if ($data['sale_type'] === 'credit' && ! $request->boolean('take_payment')) {
            $payments = [];
        }

        $invoice = SalesService::createInvoice($data, $request->input('lines'), $request->boolean('deliver_now'), $payments);

        return redirect()->route('invoices.show', $invoice)->with('success', "Invoice {$invoice->number} created.");
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer.group', 'warehouse', 'lines.product', 'lines.variant', 'deliveryNotes', 'creditNotes', 'allocations.payment.lines', 'salesOrder', 'user']);

        return view('sales.invoices.show', [
            'invoice' => $invoice,
            'byProduct' => $invoice->lines->groupBy('product_id'),
        ]);
    }

    public function print(Invoice $invoice)
    {
        $invoice->load(['customer', 'lines.product.brand', 'lines.variant', 'user']);

        return view('sales.invoices.print', ['invoice' => $invoice, 'byProduct' => $invoice->lines->groupBy('product_id')]);
    }

    /** Customers with the data the form needs for pricing and credit checks. */
    public static function customerOptions()
    {
        return Customer::with('group')->where('is_active', true)->orderBy('name')->get()->map(function ($c) {
            $c->setAttribute('ui_balance', $c->balance());
            $c->setAttribute('ui_overdue', $c->overdueAmount());

            return $c;
        });
    }
}
