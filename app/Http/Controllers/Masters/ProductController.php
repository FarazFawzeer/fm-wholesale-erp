<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CustomerGroup;
use App\Models\Product;
use App\Models\PurchaseOrderItem;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Support\Catalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = $this->filtered($request)
            ->with(['brand', 'category'])
            ->withSum('stocks as on_hand', 'qty')
            ->orderBy('code')
            ->paginate(25)->withQueryString();

        return view('masters.products.index', [
            'products' => $products,
            'brands' => Brand::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('masters.products.form', $this->formData(new Product([
            'code' => static::nextCode(), 'reorder_level' => 24, 'is_active' => true,
        ])));
    }

    public function store(Request $request)
    {
        $product = DB::transaction(function () use ($request) {
            $product = Product::create($this->validated($request));
            $product->stockItem();

            return $product;
        });

        return redirect()->route('products.show', $product)->with('success', 'Product saved.');
    }

    /** Quick add from a pop-up (purchase order, invoice, pre-order...). Returns the product for the item list. */
    public function quickStore(Request $request)
    {
        $product = DB::transaction(function () use ($request) {
            $product = Product::create($this->validated($request) + ['is_active' => true]);
            $product->stockItem();

            return $product;
        });

        return response()->json(collect(Catalog::forEditor())->firstWhere('id', $product->id));
    }

    /** Next free product code, e.g. FM-0017. */
    public function nextCodeJson()
    {
        return response()->json(['code' => static::nextCode()]);
    }

    public static function nextCode(): string
    {
        $max = Product::where('code', 'like', 'FM-%')->pluck('code')
            ->map(fn ($c) => (int) preg_replace('/\D/', '', $c))->max() ?? 0;

        return 'FM-' . str_pad($max + 1, 4, '0', STR_PAD_LEFT);
    }

    public function show(Product $product)
    {
        $product->load(['brand', 'category', 'variants.stocks', 'priceListItems']);

        return view('masters.products.show', [
            'product' => $product,
            'warehouses' => Warehouse::orderBy('id')->get(),
            'groups' => CustomerGroup::orderBy('id')->get(),
            'movements' => StockMovement::with(['variant', 'warehouse'])
                ->whereIn('product_variant_id', $product->variants->pluck('id'))
                ->latest('date')->latest('id')->limit(15)->get(),
            'purchases' => PurchaseOrderItem::with('purchaseOrder.supplier')
                ->where('product_id', $product->id)
                ->selectRaw('purchase_order_id, SUM(qty) as qty, SUM(received_qty) as received, MAX(unit_price_fx) as fx, MAX(landed_unit_cost) as landed')
                ->groupBy('purchase_order_id')->orderByDesc('purchase_order_id')->get(),
        ]);
    }

    public function edit(Product $product)
    {
        return view('masters.products.form', $this->formData($product));
    }

    public function update(Request $request, Product $product)
    {
        DB::transaction(function () use ($request, $product) {
            $product->update($this->validated($request, $product));
            $product->stockItem();
        });

        return redirect()->route('products.show', $product)->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $used = StockMovement::whereIn('product_variant_id', $product->variants()->pluck('id'))->exists()
            || PurchaseOrderItem::where('product_id', $product->id)->exists();
        if ($used) {
            $product->update(['is_active' => false]);

            return redirect()->route('products.index')->with('success', 'Product has history, so it was hidden instead of deleted.');
        }
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }

    // ---------- Bulk import ----------

    public function importForm()
    {
        return view('masters.products.import');
    }

    public function template()
    {
        $rows = [
            ['code', 'name', 'brand', 'category', 'colour', 'size_from', 'size_to', 'selling_price', 'min_price', 'reorder_level'],
            ['FM-0101', 'Canvas Loafer', 'Walkmaxx', "Men's Casual", 'Olive', '39', '44', '4500', '', '24'],
            ['FM-0102', 'School Shoe', 'Kidzo', 'Kids Shoes', 'Black', '28', '44', '2900', '2500', '24'],
        ];

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, 'fm-product-import-template.csv', ['Content-Type' => 'text/csv']);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:5120']);
        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $header = array_map(fn ($h) => strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', $h))), fgetcsv($handle) ?: []);
        $created = 0;
        $updated = 0;
        $errors = [];
        $line = 1;

        DB::transaction(function () use ($handle, $header, &$created, &$updated, &$errors, &$line) {
            while (($row = fgetcsv($handle)) !== false) {
                $line++;
                if (! array_filter($row)) {
                    continue;
                }
                $data = array_combine($header, array_pad($row, count($header), null));
                if (empty($data['name'])) {
                    $errors[] = "Row {$line}: name is required.";
                    continue;
                }
                $code = trim((string) ($data['code'] ?? '')) ?: static::nextCode();
                $product = Product::firstOrNew(['code' => $code]);
                $product->exists ? $updated++ : $created++;
                $product->fill([
                    'name' => trim($data['name']),
                    'brand_id' => ! empty($data['brand']) ? Brand::firstOrCreate(['name' => trim($data['brand'])])->id : null,
                    'category_id' => ! empty($data['category']) ? Category::firstOrCreate(['name' => trim($data['category'])])->id : null,
                    'colour' => $data['colour'] ?? null,
                    'size_from' => $data['size_from'] ?? null,
                    'size_to' => $data['size_to'] ?? null,
                    'selling_price' => (float) ($data['selling_price'] ?? 0),
                    'min_price' => ($data['min_price'] ?? '') !== '' ? (float) $data['min_price'] : null,
                    'reorder_level' => (int) ($data['reorder_level'] ?? 0) ?: 24,
                    'is_active' => true,
                ])->save();
                $product->stockItem();
            }
        });
        fclose($handle);

        return redirect()->route('products.import')
            ->with('success', "Import finished: {$created} new, {$updated} updated.")
            ->with('import_errors', $errors);
    }

    // ---------- Bulk price update ----------

    public function bulkPriceForm(Request $request)
    {
        $preview = null;
        if ($request->filled('action')) {
            $preview = $this->filtered($request)->with(['brand', 'category'])->orderBy('code')->get()
                ->map(fn ($p) => ['product' => $p, 'new' => $this->newPrice($p, $request)]);
        }

        return view('masters.products.bulk-price', [
            'brands' => Brand::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'preview' => $preview,
        ]);
    }

    public function bulkPrice(Request $request)
    {
        $request->validate(['action' => 'required|in:markup,percent,amount', 'products' => 'required|array']);
        $count = 0;
        foreach (Product::whereIn('id', $request->products)->get() as $product) {
            $product->update(['selling_price' => $this->newPrice($product, $request)]);
            $count++;
        }

        return redirect()->route('products.bulk-price')->with('success', "Selling price updated for {$count} products.");
    }

    // ---------- Helpers ----------

    protected function newPrice(Product $p, Request $request): float
    {
        $value = (float) $request->input('value', 0);
        $price = match ($request->input('action')) {
            'markup' => $p->cost_price * (1 + ($request->filled('value') ? $value : $p->effectiveMarkup()) / 100),
            'percent' => $p->selling_price * (1 + $value / 100),
            'amount' => $p->selling_price + $value,
            default => $p->selling_price,
        };
        $round = max(1, (int) $request->input('round', 10));

        return max(0, round($price / $round) * $round);
    }

    protected function filtered(Request $request)
    {
        return Product::query()
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('code', 'like', "%$s%")->orWhere('name', 'like', "%$s%")->orWhere('colour', 'like', "%$s%")))
            ->when($request->brand_id, fn ($q, $v) => $q->where('brand_id', $v))
            ->when($request->category_id, fn ($q, $v) => $q->where('category_id', $v))
            ->when($request->status === 'inactive', fn ($q) => $q->where('is_active', false), fn ($q) => $q->when($request->status !== 'all', fn ($w) => $w->where('is_active', true)));
    }

    protected function formData(Product $product): array
    {
        return [
            'product' => $product,
            'brands' => Brand::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'colours' => Product::whereNotNull('colour')->distinct()->orderBy('colour')->pluck('colour'),
        ];
    }

    /** Validates the product form. "New brand / category" typed in the form are created on the spot. */
    protected function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'code' => ['nullable', 'string', 'max:40', Rule::unique('products')->ignore($product)],
            'name' => 'required|string|max:255',
            'brand_id' => 'nullable|exists:brands,id',
            'new_brand' => 'nullable|string|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'new_category' => 'nullable|string|max:100',
            'colour' => 'nullable|string|max:50',
            'size_from' => 'nullable|string|max:10',
            'size_to' => 'nullable|string|max:10',
            'selling_price' => 'required|numeric|min:0',
            'min_price' => 'nullable|numeric|min:0',
            'markup_percent' => 'nullable|numeric|min:0|max:500',
            'reorder_level' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);
        if (! empty($data['new_brand'])) {
            $data['brand_id'] = Brand::firstOrCreate(['name' => trim($data['new_brand'])])->id;
        }
        if (! empty($data['new_category'])) {
            $data['category_id'] = Category::firstOrCreate(['name' => trim($data['new_category'])])->id;
        }
        unset($data['new_brand'], $data['new_category']);
        $data['code'] = trim((string) ($data['code'] ?? '')) ?: ($product?->code ?? static::nextCode());
        $data['reorder_level'] = $data['reorder_level'] ?? 24;
        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        return $data;
    }
}
