<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\TradeService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with('supplier')
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('number', 'like', "%$s%")->orWhere('supplier_bill_no', 'like', "%$s%")
                ->orWhereHas('supplier', fn ($c) => $c->where('name', 'like', "%$s%"))))
            ->when($request->from, fn ($q, $d) => $q->whereDate('date', '>=', $d))
            ->when($request->to, fn ($q, $d) => $q->whereDate('date', '<=', $d))
            ->when($request->show === 'unpaid', fn ($q) => $q->unpaid());

        return view('purchases.index', [
            'purchases' => (clone $query)->latest('date')->latest('id')->paginate(30)->withQueryString(),
            'sum' => (clone $query)->selectRaw('SUM(total - returned_amount) AS total, SUM(total - returned_amount - paid_amount) AS due')->first(),
        ]);
    }

    public function create(Request $request)
    {
        return view('purchases.create', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'products' => Product::pickerList(),
            'supplierId' => old('supplier_id', $request->supplier),
            'startProduct' => $request->product,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date' => 'required|date',
            'supplier_bill_no' => 'nullable|max:60',
            'discount' => 'nullable|numeric|min:0',
            'other_costs' => 'nullable|numeric|min:0',
            'notes' => 'nullable|max:2000',
            'items' => 'required|array',
            'payments' => 'array',
        ], ['items.required' => 'Add at least one product.'], ['supplier_id' => 'supplier']);

        $purchase = TradeService::purchase($data, $request->input('items', []), $request->input('payments', []));

        return redirect()->route('purchases.show', $purchase)->with('success', 'Purchase saved. ' . $purchase->totalPairs() . ' pairs added to stock.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product', 'items.colour', 'allocations.payment.lines', 'returns', 'user']);

        return view('purchases.show', ['purchase' => $purchase, 'groups' => $purchase->items->groupBy('product_id')]);
    }
}
