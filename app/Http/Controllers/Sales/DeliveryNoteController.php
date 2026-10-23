<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\DeliveryNote;
use App\Models\Invoice;
use App\Services\SalesService;
use Illuminate\Http\Request;

class DeliveryNoteController extends Controller
{
    public function index(Request $request)
    {
        $notes = DeliveryNote::with(['customer', 'invoice', 'warehouse'])->withSum('items as pairs', 'qty')
            ->when($request->q, fn ($q, $s) => $q->where('number', 'like', "%$s%")->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%$s%")))
            ->latest('date')->latest('id')->paginate(25)->withQueryString();

        return view('sales.deliveries.index', compact('notes'));
    }

    public function create(Invoice $invoice)
    {
        $invoice->load(['customer', 'warehouse', 'lines.product', 'lines.variant.stocks']);

        return view('sales.deliveries.create', ['invoice' => $invoice, 'byProduct' => $invoice->lines->groupBy('product_id')]);
    }

    public function store(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'qty' => 'required|array',
            'vehicle_no' => 'nullable|max:30',
            'received_by' => 'nullable|max:100',
            'notes' => 'nullable|string',
        ]);
        $note = SalesService::deliver($invoice->load('lines'), $data['qty'], $data);

        return redirect()->route('deliveries.show', $note)->with('success', "Delivery note {$note->number} created and stock updated.");
    }

    public function show(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load(['customer', 'invoice', 'warehouse', 'items.line.product.brand', 'items.variant', 'user']);

        return view('sales.deliveries.show', ['note' => $deliveryNote, 'byProduct' => $deliveryNote->items->groupBy(fn ($i) => $i->line->product_id)]);
    }
}
