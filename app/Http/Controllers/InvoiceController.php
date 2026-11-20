<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\TradeService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('customer')
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('number', 'like', "%$s%")
                ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%$s%")->orWhere('shop_name', 'like', "%$s%"))))
            ->when($request->from, fn ($q, $d) => $q->whereDate('date', '>=', $d))
            ->when($request->to, fn ($q, $d) => $q->whereDate('date', '<=', $d))
            ->when($request->show === 'unpaid', fn ($q) => $q->unpaid());

        return view('invoices.index', [
            'invoices' => (clone $query)->withSum('items as pairs', 'qty')->latest('date')->latest('id')->paginate(30)->withQueryString(),
            'sum' => (clone $query)->selectRaw('SUM(total - returned_amount) AS total, SUM(GREATEST(total - returned_amount - paid_amount, 0)) AS due')->first(),
        ]);
    }

    public function create(Request $request)
    {
        return view('invoices.create', [
            'customers' => Customer::withDue()->where('is_active', true)->orderBy('name')->get(),
            'products' => Product::pickerList(),
            'categories' => \App\Models\Category::whereHas('products', fn ($q) => $q->where('is_active', true))->orderBy('name')->get(['id', 'name']),
            'customerId' => old('customer_id', $request->customer),
            'startProduct' => old('items') ? null : $request->product,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'date' => 'required|date',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|max:2000',
            'items' => 'required|array',
            'payments' => 'array',
        ], ['items.required' => 'Add at least one product.'], ['customer_id' => 'customer']);

        $later = $request->input('pay_when') === 'later';
        $invoice = TradeService::invoice($data, $request->input('items', []), $later ? [] : $request->input('payments', []));

        return redirect()->route('invoices.show', $invoice)->with('success', 'Sale saved. ' . $invoice->totalPairs() . ' pairs taken out of stock.'
            . ($invoice->balance() > 0 ? ' Send the invoice now - you can record the payment when the customer pays.' : ''));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product', 'items.colour', 'allocations.payment.lines.cheque', 'returns', 'user']);

        return view('invoices.show', [
            'invoice' => $invoice,
            'groups' => $invoice->items->groupBy('product_id'),
            'customerDue' => $invoice->customer->balance(),
        ]);
    }

    public function print(Invoice $invoice)
    {
        return $this->printable($invoice, false);
    }

    /** The link sent to the customer - opens without logging in. */
    public function publicShow(string $token)
    {
        $invoice = Invoice::where('share_token', $token)->firstOrFail();

        return $this->printable($invoice, true);
    }

    /** The invoice as a PDF file (sent to the customer on WhatsApp). */
    public function pdf(Invoice $invoice)
    {
        return $this->pdfFile($invoice);
    }

    public function publicPdf(string $token)
    {
        return $this->pdfFile(Invoice::where('share_token', $token)->firstOrFail());
    }

    protected function pdfFile(Invoice $invoice)
    {
        $invoice->load(['customer', 'items.product.colours', 'items.colour', 'user']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'groups' => $invoice->items->groupBy('product_id'),
        ])->setPaper('a4');

        return $pdf->stream('Invoice-' . $invoice->number . '.pdf');
    }

    protected function printable(Invoice $invoice, bool $public)
    {
        $invoice->load(['customer', 'items.product', 'items.colour', 'allocations.payment.lines.cheque', 'user']);

        return view('invoices.print', [
            'invoice' => $invoice,
            'groups' => $invoice->items->groupBy('product_id'),
            'public' => $public,
        ]);
    }
}
