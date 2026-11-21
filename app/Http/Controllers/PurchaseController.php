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

        $missing = $purchase->missingPairs();

        return redirect()->route('purchases.show', $purchase)->with('success', 'Purchase saved. ' . ($purchase->totalPairs() - $missing) . ' pairs added to stock.'
            . ($missing ? " {$missing} missing / damaged pairs recorded." : ''));
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'items.product', 'items.colour', 'allocations.payment.lines', 'returns', 'user']);

        return view('purchases.show', ['purchase' => $purchase, 'groups' => $purchase->items->groupBy('product_id')]);
    }

    /**
     * Record (or correct) pairs that did not arrive - found after the purchase was saved.
     * missing[item_id] = total missing pairs on that line, reason[item_id] = why.
     */
    public function missing(Request $request, Purchase $purchase)
    {
        $data = $request->validate([
            'missing' => 'array',
            'missing.*' => 'nullable|integer|min:0',
            'reason' => 'array',
            'reason.*' => 'nullable|max:60',
        ]);
        $changed = 0;
        \Illuminate\Support\Facades\DB::transaction(function () use ($purchase, $data, &$changed) {
            foreach ($purchase->items as $item) {
                if (! array_key_exists($item->id, $data['missing'] ?? [])) {
                    continue;
                }
                $new = min((int) $data['missing'][$item->id], $item->qty - $item->returned_qty);
                $reason = trim((string) ($data['reason'][$item->id] ?? '')) ?: ($item->missing_reason ?: 'Missing');
                $diff = $new - $item->missing_qty;
                if ($diff === 0 && $reason === $item->missing_reason) {
                    continue;
                }
                if ($diff !== 0) {
                    \App\Services\StockService::move($item->product_colour_id, -$diff, 'missing', (float) $item->unit_cost, $purchase,
                        $diff > 0 ? $reason : 'Found (was recorded missing)');
                }
                $item->update(['missing_qty' => $new, 'missing_reason' => $new ? mb_substr($reason, 0, 60) : null]);
                $changed++;
            }
        });

        return back()->with('success', $changed ? 'Missing / damaged pairs saved. Stock updated.' : 'Nothing changed.');
    }
}
