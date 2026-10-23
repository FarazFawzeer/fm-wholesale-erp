<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\StockAdjustment;
use App\Models\Warehouse;
use App\Services\NumberService;
use App\Services\StockService;
use App\Support\Catalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Count corrections, damaged goods, write-offs, losses and returns to supplier.
 * Quantities are entered as positive pairs; the reason decides the direction.
 */
class StockAdjustmentController extends Controller
{
    /** reason => [sellable change sign, damaged change sign] */
    protected const EFFECT = [
        'count' => null,           // +/- as entered (signed)
        'damaged' => [-1, 1],      // move sellable -> damaged
        'damaged_writeoff' => [0, -1],
        'lost' => [-1, 0],
        'found' => [1, 0],
        'supplier_return' => [-1, 0],
    ];

    public function index(Request $request)
    {
        $adjustments = StockAdjustment::with(['warehouse', 'user'])->withSum('items as pairs', 'qty')
            ->when($request->reason, fn ($q, $r) => $q->where('reason', $r))
            ->latest('date')->latest('id')->paginate(25)->withQueryString();

        return view('inventory.adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        return view('inventory.adjustments.create', [
            'warehouses' => Warehouse::orderBy('id')->get(),
            'catalog' => Catalog::forEditor(),
            'initial' => old('lines') ? array_values(old('lines')) : [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'date' => 'required|date',
            'reason' => 'required|in:' . implode(',', array_keys(StockAdjustment::REASONS)),
            'direction' => 'nullable|in:add,remove',
            'notes' => 'nullable|string',
            'lines' => 'required|array',
        ]);

        $adj = DB::transaction(function () use ($data) {
            $a = StockAdjustment::create(collect($data)->except(['lines', 'direction'])->all() + ['number' => NumberService::next('ADJ'), 'user_id' => auth()->id()]);
            $effect = self::EFFECT[$data['reason']] ?? [($data['direction'] ?? 'remove') === 'add' ? 1 : -1, 0];
            $value = 0;

            foreach ($data['lines'] as $line) {
                foreach (($line['qty'] ?? []) as $variantId => $qty) {
                    $qty = (int) $qty;
                    if ($qty <= 0) {
                        continue;
                    }
                    $variant = ProductVariant::with('product')->findOrFail($variantId);
                    $cost = $variant->product->cost_price;
                    StockService::move($variant->id, $a->warehouse_id, $effect[0] * $qty, 'adjustment', $cost, $a, $a->notes, $a->date);
                    StockService::moveDamaged($variant->id, $a->warehouse_id, $effect[1] * $qty, 'adjustment', $cost, $a, $a->notes, $a->date);
                    $signed = $effect[0] !== 0 ? $effect[0] * $qty : -$qty;
                    $a->items()->create(['product_variant_id' => $variant->id, 'qty' => $signed, 'unit_cost' => $cost]);
                    $value += $signed * $cost;
                }
            }
            if (! $a->items()->exists()) {
                throw ValidationException::withMessages(['lines' => 'Enter at least one quantity.']);
            }
            $a->update(['value' => $value]);

            return $a;
        });

        return redirect()->route('stock-adjustments.show', $adj)->with('success', "Adjustment {$adj->number} posted.");
    }

    public function show(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load(['warehouse', 'user', 'items.variant.product']);

        return view('inventory.adjustments.show', ['a' => $stockAdjustment, 'byProduct' => $stockAdjustment->items->groupBy(fn ($i) => $i->variant->product_id)]);
    }
}
