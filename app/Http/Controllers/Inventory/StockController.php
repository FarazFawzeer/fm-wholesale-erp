<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /** Stock on hand: one row per product with a size-by-size breakdown. */
    public function index(Request $request)
    {
        $warehouseId = $request->warehouse_id;
        $products = Product::with(['brand', 'category', 'variants.stocks'])
            ->where('is_active', true)
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('code', 'like', "%$s%")->orWhere('name', 'like', "%$s%")->orWhere('colour', 'like', "%$s%")))
            ->when($request->brand_id, fn ($q, $v) => $q->where('brand_id', $v))
            ->when($request->category_id, fn ($q, $v) => $q->where('category_id', $v))
            ->orderBy('code')->get()
            ->map(function ($p) use ($warehouseId) {
                $sizes = $p->variants->map(fn ($v) => [
                    'size' => $v->size,
                    'qty' => (int) $v->stocks->when($warehouseId, fn ($c) => $c->where('warehouse_id', $warehouseId))->sum('qty'),
                    'damaged' => (int) $v->stocks->when($warehouseId, fn ($c) => $c->where('warehouse_id', $warehouseId))->sum('damaged_qty'),
                ]);
                $p->setAttribute('size_stock', $sizes);
                $p->setAttribute('on_hand', $sizes->sum('qty'));
                $p->setAttribute('damaged', $sizes->sum('damaged'));

                return $p;
            })
            ->when($request->level === 'low', fn ($c) => $c->filter(fn ($p) => $p->on_hand <= $p->reorder_level))
            ->when($request->level === 'out', fn ($c) => $c->filter(fn ($p) => $p->size_stock->contains('qty', 0)));

        return view('inventory.stock.index', [
            'products' => $products,
            'warehouses' => Warehouse::orderBy('id')->get(),
            'brands' => Brand::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'totals' => [
                'pairs' => $products->sum('on_hand'),
                'value' => $products->sum(fn ($p) => $p->on_hand * $p->cost_price),
                'damaged' => $products->sum('damaged'),
            ],
        ]);
    }

    public function movements(Request $request)
    {
        $movements = StockMovement::with(['variant.product', 'warehouse', 'user'])
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->when($request->warehouse_id, fn ($q, $w) => $q->where('warehouse_id', $w))
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('reference_no', 'like', "%$s%")->orWhereHas('variant', fn ($v) => $v->where('sku', 'like', "%$s%"))))
            ->when($request->from, fn ($q, $d) => $q->whereDate('date', '>=', $d))
            ->when($request->to, fn ($q, $d) => $q->whereDate('date', '<=', $d))
            ->latest('date')->latest('id')->paginate(40)->withQueryString();

        return view('inventory.stock.movements', [
            'movements' => $movements,
            'warehouses' => Warehouse::orderBy('id')->get(),
            'types' => StockMovement::distinct()->orderBy('type')->pluck('type'),
        ]);
    }
}
