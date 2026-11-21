<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

/** Send customers a catalogue of in-stock products (photo + details, no prices) as a PDF on WhatsApp. */
class CatalogueController extends Controller
{
    public const MAX = 60; // keeps the PDF quick to make and small to send

    public function index(Request $request)
    {
        $products = Product::with(['colours', 'brand', 'category'])->where('is_active', true)->orderBy('name')->get()
            ->filter(fn ($p) => $p->totalStock() > 0)->values();

        return view('catalogue.index', [
            'products' => $products,
            'categories' => Category::whereIn('id', $products->pluck('category_id')->filter()->unique())->orderBy('name')->get(),
            'selected' => array_map('intval', array_filter(explode(',', (string) $request->ids))),
        ]);
    }

    /** ?ids=1,2,3 [&pairs=1] -> PDF */
    public function pdf(Request $request)
    {
        @set_time_limit(120);
        @ini_set('memory_limit', '512M');
        $ids = array_slice(array_map('intval', array_filter(explode(',', (string) $request->ids))), 0, self::MAX);
        $products = Product::with(['colours', 'brand', 'category'])->whereIn('id', $ids)->get()
            ->sortBy(fn ($p) => array_search($p->id, $ids))->values();
        abort_if($products->isEmpty(), 404, 'Choose at least one product.');

        $name = 'Catalogue-' . now()->format('Y-m-d') . '.pdf';
        try {
            $fonts = storage_path('fonts');
            if (! is_dir($fonts)) {
                @mkdir($fonts, 0755, true);
            }
            $content = \Barryvdh\DomPDF\Facade\Pdf::setOptions(['font_dir' => $fonts, 'font_cache' => $fonts, 'chroot' => [base_path(), public_path()]])
                ->loadView('catalogue.pdf', ['products' => $products, 'pairs' => $request->boolean('pairs')])
                ->setPaper('a4')->output();
        } catch (\Throwable $e) {
            report($e);

            return response('The PDF could not be made: ' . $e->getMessage(), 500)
                ->header('Content-Type', 'text/plain; charset=utf-8')
                ->header('X-Pdf-Error', mb_substr(preg_replace('/\s+/', ' ', $e->getMessage()), 0, 180));
        }

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => ($request->boolean('download') ? 'attachment' : 'inline') . '; filename="' . $name . '"',
            'Content-Length' => strlen($content),
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
