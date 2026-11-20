<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColour;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['colours', 'brand', 'category'])
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('code', 'like', "%$s%")
                ->orWhereHas('colours', fn ($c) => $c->where('colour', 'like', "%$s%"))))
            ->when($request->brand, fn ($q, $b) => $q->where('brand_id', $b))
            ->when($request->category, fn ($q, $c) => $q->where('category_id', $c))
            ->when($request->show !== 'hidden', fn ($q) => $q->where('is_active', true), fn ($q) => $q->where('is_active', false))
            ->orderBy('name')
            ->get();

        if ($request->show === 'low') {
            $products = $products->filter->isLow()->values();
        }

        return view('products.index', [
            'products' => $products,
            'brands' => Brand::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('products.form', [
            'product' => new Product(['code' => Product::nextCode(), 'low_stock_level' => 12, 'is_active' => true]),
            'brands' => Brand::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $product = DB::transaction(function () use ($request, $data) {
            $product = Product::create($data);
            $this->savePhoto($request->file('photo'), $product);
            $this->saveColours($request, $product, (float) $request->input('opening_cost', 0));

            return $product;
        });

        return redirect()->route('products.show', $product)->with('success', 'Product saved.');
    }

    /** Quick add from the buy / sell screens. Returns the product for the item picker. */
    public function quickStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'code' => 'nullable|max:40|unique:products,code',
            'brand_name' => 'nullable|max:100',
            'size_from' => 'nullable|max:10',
            'size_to' => 'nullable|max:10',
            'selling_price' => 'nullable|numeric|min:0',
            'colour_names' => 'nullable|max:255',
            'photo' => 'nullable|image|max:8192',
        ]);
        $product = DB::transaction(function () use ($request, $data) {
            $product = Product::create([
                'code' => strtoupper(trim((string) ($data['code'] ?? ''))) ?: Product::nextCode(),
                'name' => $data['name'],
                'brand_id' => filled($data['brand_name'] ?? null) ? Brand::firstOrCreate(['name' => trim($data['brand_name'])])->id : null,
                'size_from' => $data['size_from'] ?? null,
                'size_to' => $data['size_to'] ?? null,
                'selling_price' => $data['selling_price'] ?? 0,
                'is_active' => true,
            ]);
            $this->savePhoto($request->file('photo'), $product);
            foreach (array_filter(array_map('trim', preg_split('/[,\/]+/', (string) ($data['colour_names'] ?? '')))) as $name) {
                StockService::colour($product, $name);
            }

            return $product;
        });

        return response()->json($product->load('colours', 'brand')->toPicker());
    }

    /** Quick "check price & stock" search from the top bar (any page). */
    public function lookup(Request $request)
    {
        $q = trim((string) $request->q);
        $compact = str_replace([' ', '-', '_', '.'], '', $q);
        $products = Product::with(['colours', 'brand', 'category'])
            ->when($q !== '', fn ($query) => $query->where(fn ($w) => $w
                ->where('code', 'like', "%$q%")
                ->orWhereRaw("REPLACE(REPLACE(code, '-', ''), ' ', '') LIKE ?", ["%$compact%"])
                ->orWhere('name', 'like', "%$q%")
                ->orWhereHas('brand', fn ($b) => $b->where('name', 'like', "%$q%"))
                ->orWhereHas('colours', fn ($c) => $c->where('colour', 'like', "%$q%"))))
            ->where('is_active', true)
            ->orderByRaw('CASE WHEN code = ? THEN 0 WHEN code LIKE ? THEN 1 WHEN name LIKE ? THEN 2 ELSE 3 END', [$q, "$q%", "$q%"])
            ->orderBy('name')
            ->limit(8)->get();

        return response()->json($products->map(fn ($p) => $p->toPicker() + [
            'category' => $p->category?->name,
            'stock' => $p->totalStock(),
            'low' => $p->isLow(),
            'url' => route('products.show', $p),
            'sell_url' => route('invoices.create', ['product' => $p->id]),
        ])->values());
    }

    public function show(Product $product)
    {
        $product->load(['colours', 'brand', 'category']);
        $sold = fn ($from = null) => (int) $product->invoiceItems()
            ->when($from, fn ($q) => $q->whereHas('invoice', fn ($i) => $i->where('date', '>=', $from)))
            ->sum(DB::raw('qty - returned_qty'));

        $sales = \App\Models\InvoiceItem::with(['invoice.customer', 'colour'])->where('product_id', $product->id)
            ->get()->groupBy('invoice_id')->map(fn ($rows) => [
                'invoice' => $rows->first()->invoice,
                'pairs' => $rows->sum(fn ($r) => $r->qty - $r->returned_qty),
                'price' => (float) $rows->first()->unit_price,
                'colours' => $rows->map(fn ($r) => ['name' => $r->colour->title, 'qty' => $r->qty])->all(),
            ])->sortByDesc(fn ($r) => $r['invoice']->date->format('Ymd') . str_pad($r['invoice']->id, 8, '0', STR_PAD_LEFT))->values();

        $purchases = \App\Models\PurchaseItem::with(['purchase.supplier', 'colour'])->where('product_id', $product->id)
            ->get()->groupBy('purchase_id')->map(fn ($rows) => [
                'purchase' => $rows->first()->purchase,
                'pairs' => $rows->sum('qty'),
                'price' => (float) $rows->first()->unit_price,
                'cost' => (float) $rows->first()->unit_cost,
                'colours' => $rows->map(fn ($r) => ['name' => $r->colour->title, 'qty' => $r->qty])->all(),
            ])->sortByDesc(fn ($r) => $r['purchase']->date->format('Ymd') . str_pad($r['purchase']->id, 8, '0', STR_PAD_LEFT))->values();

        return view('products.show', [
            'product' => $product,
            'movements' => \App\Models\StockMovement::with('colour')->whereIn('product_colour_id', $product->colours->pluck('id'))
                ->latest('date')->latest('id')->limit(40)->get(),
            'soldPairs' => $sold(),
            'sold30' => $sold(now()->subDays(29)->toDateString()),
            'boughtPairs' => (int) $product->purchaseItems()->sum(DB::raw('qty - returned_qty')),
            'sales' => $sales,
            'purchases' => $purchases,
            'lastSale' => $sales->first(),
            'lastPurchase' => $purchases->first(),
        ]);
    }

    public function edit(Product $product)
    {
        return view('products.form', [
            'product' => $product->load('colours'),
            'brands' => Brand::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request, $product);
        DB::transaction(function () use ($request, $data, $product) {
            $product->update($data);
            if ($request->boolean('remove_photo')) {
                $this->deletePhoto($product);
                $product->update(['image_path' => null]);
            }
            $this->savePhoto($request->file('photo'), $product);
            $this->saveColours($request, $product, (float) ($product->cost_price ?: $request->input('opening_cost', 0)));
        });

        return redirect()->route('products.show', $product)->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        if ($product->invoiceItems()->exists() || $product->purchaseItems()->exists()) {
            $product->update(['is_active' => false]);

            return redirect()->route('products.index')->with('success', 'This product has sales or purchases, so it was hidden instead of deleted.');
        }
        $this->deletePhoto($product);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }

    /** Correct the stock of one colour after counting (damaged, lost, found...). */
    public function correctStock(Request $request, Product $product)
    {
        $data = $request->validate([
            'product_colour_id' => ['required', Rule::exists('product_colours', 'id')->where('product_id', $product->id)],
            'counted' => 'required|integer|min:0',
            'reason' => 'required|max:100',
        ]);
        $colour = ProductColour::findOrFail($data['product_colour_id']);
        $diff = (int) $data['counted'] - $colour->qty;
        if ($diff === 0) {
            return back()->with('success', 'Stock is already ' . $colour->qty . ' pairs - nothing changed.');
        }
        DB::transaction(fn () => StockService::move($colour->id, $diff, 'correction', (float) $product->cost_price, null, $data['reason']));

        return back()->with('success', "{$colour->title}: stock changed by " . ($diff > 0 ? '+' : '') . "$diff to {$data['counted']} pairs.");
    }

    protected function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'max:40', Rule::unique('products')->ignore($product)],
            'name' => 'required|max:255',
            'brand_id' => 'nullable|exists:brands,id',
            'new_brand' => 'nullable|max:100',
            'category_id' => 'nullable|exists:categories,id',
            'new_category' => 'nullable|max:100',
            'size_from' => 'nullable|max:10',
            'size_to' => 'nullable|max:10',
            'selling_price' => 'required|numeric|min:0',
            'low_stock_level' => 'nullable|integer|min:0',
            'description' => 'nullable|max:2000',
            'photo' => 'nullable|image|max:8192',
            'colours' => 'array',
            'colours.*.name' => 'nullable|max:40',
            'colours.*.size' => 'nullable|max:20',
            'colours.*.qty' => 'nullable|integer|min:0',
            'opening_cost' => 'nullable|numeric|min:0',
        ], ['code.unique' => 'This item no is already used by another product.'], ['code' => 'item no', 'photo' => 'photo', 'colours.*.name' => 'colour name']);

        if (filled($data['new_brand'] ?? null)) {
            $data['brand_id'] = Brand::firstOrCreate(['name' => trim($data['new_brand'])])->id;
        }
        if (filled($data['new_category'] ?? null)) {
            $data['category_id'] = Category::firstOrCreate(['name' => trim($data['new_category'])])->id;
        }
        $data['code'] = strtoupper(trim($data['code']));
        $data['low_stock_level'] = (int) ($data['low_stock_level'] ?? 12);
        $data['is_active'] = $request->boolean('is_active', true);

        return collect($data)->except(['new_brand', 'new_category', 'photo', 'colours', 'opening_cost'])->all();
    }

    /** Colour lines (colour + size range): edit existing ones, add new ones with optional opening stock. */
    protected function saveColours(Request $request, Product $product, float $openingCost): void
    {
        foreach ($request->input('colours', []) as $row) {
            $name = ucwords(trim(preg_replace('/\s+/', ' ', (string) ($row['name'] ?? ''))));
            $size = ProductColour::cleanSize($row['size'] ?? null);
            $id = $row['id'] ?? null;
            if ($id) {
                $colour = $product->colours()->find($id);
                if (! $colour) {
                    continue;
                }
                if (! empty($row['delete']) && $colour->qty == 0 && ! $colour->movements()->whereNot('type', 'opening')->exists()) {
                    $colour->movements()->delete();
                    $colour->delete();
                } elseif ($name !== '' && ($name !== $colour->colour || $size !== $colour->size)) {
                    $taken = $product->colours()->whereKeyNot($colour->id)->where('colour', $name)
                        ->when($size, fn ($q) => $q->where('size', $size), fn ($q) => $q->whereNull('size'))->exists();
                    if ($taken) {
                        throw \Illuminate\Validation\ValidationException::withMessages(['colours' => "{$name}" . ($size ? " ({$size})" : '') . ' is already on this product.']);
                    }
                    $colour->update(['colour' => $name, 'size' => $size]);
                }
                continue;
            }
            if ($name === '') {
                continue;
            }
            $colour = StockService::colour($product, $name, $size);
            $qty = (int) ($row['qty'] ?? 0);
            if ($qty > 0) {
                StockService::receive($colour->id, $qty, $openingCost, null, 'Opening stock', null, 'opening');
            }
        }
    }

    protected function savePhoto(?UploadedFile $file, Product $product): void
    {
        if (! $file) {
            return;
        }
        $dir = public_path('uploads/products');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $this->deletePhoto($product);
        $name = Str::slug($product->code) . '-' . Str::random(6);

        // Shrink big phone photos so pages stay fast (needs the GD extension; otherwise keep the original)
        $path = null;
        if (function_exists('imagecreatefromstring') && ($img = @imagecreatefromstring(file_get_contents($file->getRealPath())))) {
            $w = imagesx($img);
            $h = imagesy($img);
            $scale = min(1, 1000 / max($w, $h));
            $out = imagecreatetruecolor((int) round($w * $scale), (int) round($h * $scale));
            imagefill($out, 0, 0, imagecolorallocate($out, 255, 255, 255));
            imagecopyresampled($out, $img, 0, 0, 0, 0, imagesx($out), imagesy($out), $w, $h);
            imagejpeg($out, $dir . '/' . $name . '.jpg', 82);
            imagedestroy($img);
            imagedestroy($out);
            $path = 'uploads/products/' . $name . '.jpg';
        }
        if (! $path) {
            $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            $file->move($dir, $name . '.' . $ext);
            $path = 'uploads/products/' . $name . '.' . $ext;
        }
        $product->update(['image_path' => $path]);
    }

    protected function deletePhoto(Product $product): void
    {
        if ($product->image_path && str_starts_with($product->image_path, 'uploads/') && is_file(public_path($product->image_path))) {
            @unlink(public_path($product->image_path));
        }
    }
}
