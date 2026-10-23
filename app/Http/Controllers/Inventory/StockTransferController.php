<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use App\Services\NumberService;
use App\Services\StockService;
use App\Support\Catalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockTransferController extends Controller
{
    public function index()
    {
        $transfers = StockTransfer::with(['from', 'to', 'user'])->withSum('items as pairs', 'qty')->latest('date')->latest('id')->paginate(25);

        return view('inventory.transfers.index', compact('transfers'));
    }

    public function create()
    {
        return view('inventory.transfers.create', [
            'warehouses' => Warehouse::orderBy('id')->get(),
            'catalog' => Catalog::forEditor(),
            'initial' => old('lines') ? array_values(old('lines')) : [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'date' => 'required|date',
            'notes' => 'nullable|string',
            'lines' => 'required|array',
        ]);

        $transfer = DB::transaction(function () use ($data) {
            $t = StockTransfer::create(collect($data)->except('lines')->all() + ['number' => NumberService::next('TRF'), 'user_id' => auth()->id()]);
            foreach ($data['lines'] as $line) {
                foreach (($line['qty'] ?? []) as $variantId => $qty) {
                    if ((int) $qty <= 0) {
                        continue;
                    }
                    $variant = ProductVariant::with('product')->findOrFail($variantId);
                    $cost = $variant->product->cost_price;
                    StockService::move($variant->id, $t->from_warehouse_id, -(int) $qty, 'transfer_out', $cost, $t, null, $t->date);
                    StockService::move($variant->id, $t->to_warehouse_id, (int) $qty, 'transfer_in', $cost, $t, null, $t->date);
                    $t->items()->create(['product_variant_id' => $variant->id, 'qty' => (int) $qty, 'unit_cost' => $cost]);
                }
            }
            if (! $t->items()->exists()) {
                throw ValidationException::withMessages(['lines' => 'Enter at least one quantity to transfer.']);
            }

            return $t;
        });

        return redirect()->route('stock-transfers.show', $transfer)->with('success', "Transfer {$transfer->number} completed.");
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['from', 'to', 'user', 'items.variant.product']);

        return view('inventory.transfers.show', ['t' => $stockTransfer, 'byProduct' => $stockTransfer->items->groupBy(fn ($i) => $i->variant->product_id)]);
    }
}
