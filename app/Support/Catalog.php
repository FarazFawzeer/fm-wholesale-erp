<?php

namespace App\Support;

use App\Models\CustomerGroup;
use App\Models\Product;
use App\Models\PurchaseOrderItem;
use App\Models\Stock;

/** Product data handed to the size-grid line editor (JS). */
class Catalog
{
    public static function forEditor(bool $withStock = true, ?int $supplierId = null): array
    {
        $products = Product::with(['brand', 'variants', 'priceListItems'])->where('is_active', true)->orderBy('code')->get();
        $groups = CustomerGroup::all();
        $stock = $withStock
            ? Stock::get(['product_variant_id', 'warehouse_id', 'qty'])->groupBy('product_variant_id')
            : collect();
        $lastFx = PurchaseOrderItem::query()
            ->when($supplierId, fn ($q) => $q->whereHas('purchaseOrder', fn ($p) => $p->where('supplier_id', $supplierId)))
            ->orderByDesc('id')->get(['product_id', 'unit_price_fx'])->unique('product_id')->pluck('unit_price_fx', 'product_id');

        return $products->map(fn (Product $p) => [
            'id' => $p->id,
            'code' => $p->code,
            'name' => trim($p->name . ($p->colour ? ' - ' . $p->colour : '')),
            'brand' => $p->brand?->name,
            'ppc' => $p->pairs_per_carton,
            'price' => (float) $p->selling_price,
            'cost' => (float) $p->cost_price,
            'min_price' => $p->minimumPrice(),
            'last_fx_price' => (float) ($lastFx[$p->id] ?? 0),
            'group_prices' => $groups->mapWithKeys(fn ($g) => [$g->id => $p->priceForGroup($g)]),
            'variants' => $p->variants->map(fn ($v) => [
                'id' => $v->id,
                'size' => $v->size,
                'stock' => $stock->get($v->id, collect())->pluck('qty', 'warehouse_id'),
            ])->values(),
        ])->values()->all();
    }
}
