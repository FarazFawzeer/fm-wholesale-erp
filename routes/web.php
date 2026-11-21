<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MoneyController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

// Login / logout
require __DIR__ . '/auth.php';

// Phone app (Add to Home Screen) - the manifest uses the business name from Settings
Route::get('manifest.webmanifest', function () {
    $name = setting('company_name', 'FM Shoes');

    return response()->json([
        'name' => $name,
        'short_name' => \Illuminate\Support\Str::limit($name, 12, ''),
        'description' => 'Buy, sell, stock and dues for ' . $name,
        'id' => '/',
        'start_url' => url('/') . '/?source=app',
        'scope' => url('/') . '/',
        'display' => 'standalone',
        'orientation' => 'any',
        'background_color' => '#f5f6f8',
        'theme_color' => '#ffffff',
        'icons' => [
            ['src' => asset('images/app/icon-192.png'), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'],
            ['src' => asset('images/app/icon-512.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any'],
            ['src' => asset('images/app/maskable-512.png'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
        ],
        'shortcuts' => [
            ['name' => 'Sell', 'url' => url('invoices/create'), 'icons' => [['src' => asset('images/app/icon-192.png'), 'sizes' => '192x192']]],
            ['name' => 'Check price & stock', 'url' => url('products'), 'icons' => [['src' => asset('images/app/icon-192.png'), 'sizes' => '192x192']]],
            ['name' => 'Dues', 'url' => url('dues'), 'icons' => [['src' => asset('images/app/icon-192.png'), 'sizes' => '192x192']]],
        ],
    ], 200, ['Content-Type' => 'application/manifest+json', 'Cache-Control' => 'public, max-age=3600'], JSON_UNESCAPED_SLASHES);
})->name('manifest');

// Invoice link sent to customers (WhatsApp) - opens without logging in
Route::get('i/{token}/pdf', [InvoiceController::class, 'publicPdf'])->where('token', '[A-Za-z0-9]{32}')->middleware('throttle:60,1')->name('invoice.public.pdf');
Route::get('i/{token}', [InvoiceController::class, 'publicShow'])->where('token', '[A-Za-z0-9]{32}')->middleware('throttle:60,1')->name('invoice.public');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('root');

    // ---- Products ----
    Route::get('products/next-code', fn () => response()->json(['code' => Product::nextCode()]))->name('products.next-code');
    Route::get('products/lookup', [ProductController::class, 'lookup'])->name('products.lookup');
    Route::post('products/quick', [ProductController::class, 'quickStore'])->name('products.quick');
    Route::post('products/{product}/correct', [ProductController::class, 'correctStock'])->name('products.correct');
    Route::get('catalogue', [\App\Http\Controllers\CatalogueController::class, 'index'])->name('catalogue.index');
    Route::get('catalogue/pdf', [\App\Http\Controllers\CatalogueController::class, 'pdf'])->name('catalogue.pdf');
    Route::resource('products', ProductController::class);

    // ---- People ----
    Route::post('customers/quick', [CustomerController::class, 'quickStore'])->name('customers.quick');
    Route::get('customers/{customer}/statement', [ReportController::class, 'statement'])->name('customers.statement');
    Route::resource('customers', CustomerController::class);
    Route::post('suppliers/quick', [SupplierController::class, 'quickStore'])->name('suppliers.quick');
    Route::resource('suppliers', SupplierController::class);

    // ---- Buy ----
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('purchases/{purchase}/missing', [PurchaseController::class, 'missing'])->name('purchases.missing');

    // ---- Sell ----
    Route::resource('invoices', InvoiceController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

    // ---- Returns (from customers / to suppliers) ----
    Route::get('returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::get('returns/new', [ReturnController::class, 'create'])->name('returns.create');
    Route::post('returns/pick', [ReturnController::class, 'pick'])->name('returns.pick');
    Route::get('returns/customer/{invoice}', [ReturnController::class, 'customerForm'])->name('returns.customer');
    Route::post('returns/customer/{invoice}', [ReturnController::class, 'customerStore'])->name('returns.customer.store');
    Route::get('returns/supplier/{purchase}', [ReturnController::class, 'supplierForm'])->name('returns.supplier');
    Route::post('returns/supplier/{purchase}', [ReturnController::class, 'supplierStore'])->name('returns.supplier.store');
    Route::get('returns/{return}', [ReturnController::class, 'show'])->name('returns.show');
    Route::get('returns/{return}/print', [ReturnController::class, 'print'])->name('returns.print');

    // ---- Money ----
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/receive', [PaymentController::class, 'receive'])->name('payments.receive');
    Route::get('payments/pay', [PaymentController::class, 'pay'])->name('payments.pay');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('dues', [MoneyController::class, 'dues'])->name('dues');
    Route::get('cheques', [MoneyController::class, 'cheques'])->name('cheques');
    Route::post('cheques/{cheque}/status', [MoneyController::class, 'chequeStatus'])->name('cheques.status');
    Route::get('expenses', [MoneyController::class, 'expenses'])->name('expenses.index');
    Route::post('expenses', [MoneyController::class, 'storeExpense'])->name('expenses.store');
    Route::delete('expenses/{expense}', [MoneyController::class, 'destroyExpense'])->name('expenses.destroy');

    // ---- Reports ----
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/best-sellers', [ReportController::class, 'products'])->name('reports.products');
    Route::get('reports/stock', [ReportController::class, 'stock'])->name('reports.stock');
    Route::get('reports/missing', [ReportController::class, 'missing'])->name('reports.missing');

    // ---- Settings ----
    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings-terms', [SettingController::class, 'terms'])->name('settings.terms');
    Route::post('settings/{type}', [SettingController::class, 'storeItem'])->name('settings.items.store');
    Route::put('settings/{type}/{id}', [SettingController::class, 'updateItem'])->name('settings.items.update');
    Route::delete('settings/{type}/{id}', [SettingController::class, 'destroyItem'])->name('settings.items.destroy');
    Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
});
