<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\NumberService;
use App\Services\PurchaseService;
use App\Support\Catalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = PurchaseOrder::with('supplier')->withSum('items as pairs', 'qty')->withSum('items as received', 'received_qty')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->supplier_id, fn ($q, $s) => $q->where('supplier_id', $s))
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('number', 'like', "%$s%")->orWhere('shipment_ref', 'like', "%$s%")))
            ->latest('order_date')->latest('id')->paginate(20)->withQueryString();

        return view('purchasing.orders.index', [
            'orders' => $orders,
            'suppliers' => Supplier::orderBy('name')->get(),
            'counts' => PurchaseOrder::selectRaw('status, COUNT(*) c')->groupBy('status')->pluck('c', 'status'),
        ]);
    }

    public function create(Request $request)
    {
        return view('purchasing.orders.form', $this->formData(new PurchaseOrder([
            'supplier_id' => $request->supplier_id,
            'order_date' => now(),
            'expected_date' => now()->addDays(30),
            'exchange_rate' => 300,
            'currency' => 'USD',
        ])));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $po = DB::transaction(function () use ($data, $request) {
            $po = PurchaseOrder::create($data + [
                'number' => NumberService::next('PO'),
                'status' => $request->input('save_as') === 'ordered' ? 'ordered' : 'draft',
                'user_id' => auth()->id(),
            ]);
            PurchaseService::syncItems($po, $request->input('lines', []));

            return $po;
        });

        return redirect()->route('purchase-orders.show', $po)->with('success', "Purchase order {$po->number} saved. Next: add import costs.");
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $po = $purchaseOrder->load(['supplier', 'items.product.brand', 'items.variant', 'charges', 'grns.warehouse', 'user']);
        $allocations = \App\Models\PaymentAllocation::with('payment.lines')->where('purchase_order_id', $po->id)->get();

        return view('purchasing.orders.show', [
            'po' => $po,
            'byProduct' => $po->items->groupBy('product_id'),
            'warehouses' => Warehouse::orderBy('id')->get(),
            'allocations' => $allocations,
        ]);
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        abort_unless($purchaseOrder->isEditable(), 403, 'Received orders cannot be edited.');

        return view('purchasing.orders.form', $this->formData($purchaseOrder->load('items')));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        abort_unless($purchaseOrder->isEditable(), 403);
        DB::transaction(function () use ($request, $purchaseOrder) {
            $purchaseOrder->update($this->validated($request) + (
                $request->input('save_as') === 'ordered' && $purchaseOrder->status === 'draft' ? ['status' => 'ordered'] : []
            ));
            PurchaseService::syncItems($purchaseOrder, $request->input('lines', []));
        });

        return redirect()->route('purchase-orders.show', $purchaseOrder)->with('success', 'Purchase order updated.');
    }

    public function status(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate(['status' => 'required|in:ordered,cancelled']);
        abort_unless($purchaseOrder->isEditable(), 403);
        $purchaseOrder->update(['status' => $request->status]);

        return back()->with('success', 'Purchase order marked as ' . $request->status . '.');
    }

    public function costing(PurchaseOrder $purchaseOrder)
    {
        return view('purchasing.orders.costing', [
            'po' => $purchaseOrder->load(['supplier', 'items.product', 'charges']),
        ]);
    }

    public function saveCosting(Request $request, PurchaseOrder $purchaseOrder)
    {
        abort_unless($purchaseOrder->isEditable(), 403, 'Costing is locked after goods are received.');
        $data = $request->validate([
            'exchange_rate' => 'required|numeric|min:0.0001',
            'allocation_method' => 'required|in:value,qty',
            'charges' => 'array',
            'charges.*.type' => 'required|string',
            'charges.*.amount' => 'nullable|numeric|min:0',
            'charges.*.description' => 'nullable|string|max:255',
        ]);
        DB::transaction(function () use ($purchaseOrder, $data) {
            $purchaseOrder->update([
                'exchange_rate' => $data['exchange_rate'],
                'allocation_method' => $data['allocation_method'],
                'status' => in_array($purchaseOrder->status, ['ordered', 'draft']) ? 'costed' : $purchaseOrder->status,
            ]);
            PurchaseService::syncCharges($purchaseOrder, $data['charges'] ?? []);
        });

        return redirect()->route('purchase-orders.costing', $purchaseOrder)->with('success', 'Import costing saved. Landed cost per pair recalculated.');
    }

    protected function formData(PurchaseOrder $po): array
    {
        $grid = $po->exists
            ? $po->items->groupBy('product_id')->map(fn ($rows, $pid) => [
                'product_id' => (int) $pid,
                'unit_price' => (float) $rows->first()->unit_price_fx,
                'qty' => $rows->pluck('qty', 'product_variant_id')->all(),
            ])->values()->all()
            : [];

        return [
            'po' => $po,
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
            'catalog' => Catalog::forEditor(false),
            'initial' => old('lines') ? array_values(old('lines')) : $grid,
        ];
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'currency' => 'required|size:3',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'shipment_ref' => 'nullable|max:100',
            'notes' => 'nullable|string',
        ]);
    }
}
