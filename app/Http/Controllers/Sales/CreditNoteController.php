<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Services\SalesService;
use Illuminate\Http\Request;

/** Sales returns (goods back into stock) and price-adjustment credit notes. */
class CreditNoteController extends Controller
{
    public function index()
    {
        $notes = CreditNote::with(['customer', 'invoice'])->withSum('items as pairs', 'qty')->latest('date')->latest('id')->paginate(25);

        return view('sales.credit-notes.index', compact('notes'));
    }

    public function create(Invoice $invoice)
    {
        $invoice->load(['customer', 'lines.product', 'lines.variant']);

        return view('sales.credit-notes.create', ['invoice' => $invoice, 'byProduct' => $invoice->lines->groupBy('product_id')]);
    }

    public function store(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'qty' => 'required|array',
            'reason' => 'nullable|string|max:255',
            'restock' => 'nullable|boolean',
        ]);
        $note = SalesService::creditNote($invoice->load('lines'), $data['qty'], $data);

        return redirect()->route('credit-notes.show', $note)->with('success', "Credit note {$note->number} created for " . money($note->total) . '.');
    }

    public function show(CreditNote $creditNote)
    {
        $creditNote->load(['customer', 'invoice', 'items.line.product', 'items.variant', 'user']);

        return view('sales.credit-notes.show', ['note' => $creditNote]);
    }
}
