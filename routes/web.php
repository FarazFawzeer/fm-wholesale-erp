<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Finance\AccountController;
use App\Http\Controllers\Finance\ChequeController;
use App\Http\Controllers\Finance\CreditControlController;
use App\Http\Controllers\Finance\ExpenseController;
use App\Http\Controllers\Finance\PaymentController;
use App\Http\Controllers\Inventory\StockAdjustmentController;
use App\Http\Controllers\Inventory\StockController;
use App\Http\Controllers\Inventory\StockTransferController;
use App\Http\Controllers\Masters\CustomerController;
use App\Http\Controllers\Masters\CustomerGroupController;
use App\Http\Controllers\Masters\ProductController;
use App\Http\Controllers\Masters\SetupController;
use App\Http\Controllers\Masters\SupplierController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Purchasing\GrnController;
use App\Http\Controllers\Purchasing\PurchaseOrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Sales\CreditNoteController;
use App\Http\Controllers\Sales\DeliveryNoteController;
use App\Http\Controllers\Sales\InvoiceController;
use App\Http\Controllers\Sales\SalesOrderController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

// Guest auth routes (login, logout, password reset)
require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('root');

    // ---- Masters ----
    Route::get('products/import', [ProductController::class, 'importForm'])->name('products.import');
    Route::post('products/import', [ProductController::class, 'import'])->name('products.import.store');
    Route::get('products/template', [ProductController::class, 'template'])->name('products.template');
    Route::get('products/bulk-price', [ProductController::class, 'bulkPriceForm'])->name('products.bulk-price');
    Route::post('products/bulk-price', [ProductController::class, 'bulkPrice'])->name('products.bulk-price.store');
    Route::resource('products', ProductController::class);
    Route::resource('suppliers', SupplierController::class)->except('destroy');
    Route::resource('customers', CustomerController::class)->except('destroy');
    Route::get('customer-groups', [CustomerGroupController::class, 'index'])->name('customer-groups.index');
    Route::post('customer-groups', [CustomerGroupController::class, 'store'])->name('customer-groups.store');
    Route::put('customer-groups/{customerGroup}', [CustomerGroupController::class, 'update'])->name('customer-groups.update');
    Route::get('customer-groups/{customerGroup}/prices', [CustomerGroupController::class, 'prices'])->name('customer-groups.prices');
    Route::post('customer-groups/{customerGroup}/prices', [CustomerGroupController::class, 'savePrices'])->name('customer-groups.prices.save');
    Route::get('setup', [SetupController::class, 'index'])->name('setup.index');
    Route::post('setup/{type}', [SetupController::class, 'store'])->name('setup.store');
    Route::put('setup/{type}/{id}', [SetupController::class, 'update'])->name('setup.update');
    Route::delete('setup/{type}/{id}', [SetupController::class, 'destroy'])->name('setup.destroy');

    // ---- Purchasing & import ----
    Route::resource('purchase-orders', PurchaseOrderController::class)->except('destroy');
    Route::post('purchase-orders/{purchase_order}/status', [PurchaseOrderController::class, 'status'])->name('purchase-orders.status');
    Route::get('purchase-orders/{purchase_order}/costing', [PurchaseOrderController::class, 'costing'])->name('purchase-orders.costing');
    Route::post('purchase-orders/{purchase_order}/costing', [PurchaseOrderController::class, 'saveCosting'])->name('purchase-orders.costing.save');
    Route::post('purchase-orders/{purchase_order}/receive', [GrnController::class, 'store'])->name('grns.store');
    Route::get('grns', [GrnController::class, 'index'])->name('grns.index');
    Route::get('grns/{grn}', [GrnController::class, 'show'])->name('grns.show');
    Route::get('grns/{grn}/edit', [GrnController::class, 'edit'])->name('grns.edit');
    Route::put('grns/{grn}', [GrnController::class, 'update'])->name('grns.update');

    // ---- Inventory ----
    Route::get('stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('stock/movements', [StockController::class, 'movements'])->name('stock.movements');
    Route::resource('stock-transfers', StockTransferController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('stock-adjustments', StockAdjustmentController::class)->only(['index', 'create', 'store', 'show']);

    // ---- Sales ----
    Route::get('quotations', [SalesOrderController::class, 'index'])->defaults('type', 'quotation')->name('quotations.index');
    Route::get('quotations/create', [SalesOrderController::class, 'create'])->defaults('type', 'quotation')->name('quotations.create');
    Route::get('sales-orders', [SalesOrderController::class, 'index'])->defaults('type', 'order')->name('sales-orders.index');
    Route::get('sales-orders/create', [SalesOrderController::class, 'create'])->defaults('type', 'order')->name('sales-orders.create');
    Route::post('sales-docs', [SalesOrderController::class, 'store'])->name('sales-docs.store');
    Route::get('sales-docs/{salesOrder}', [SalesOrderController::class, 'show'])->name('sales-docs.show');
    Route::get('sales-docs/{salesOrder}/edit', [SalesOrderController::class, 'edit'])->name('sales-docs.edit');
    Route::put('sales-docs/{salesOrder}', [SalesOrderController::class, 'update'])->name('sales-docs.update');
    Route::post('sales-docs/{salesOrder}/status', [SalesOrderController::class, 'status'])->name('sales-docs.status');
    Route::post('sales-docs/{salesOrder}/convert', [SalesOrderController::class, 'convert'])->name('sales-docs.convert');

    Route::resource('invoices', InvoiceController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::get('invoices/{invoice}/deliver', [DeliveryNoteController::class, 'create'])->name('deliveries.create');
    Route::post('invoices/{invoice}/deliver', [DeliveryNoteController::class, 'store'])->name('deliveries.store');
    Route::get('deliveries', [DeliveryNoteController::class, 'index'])->name('deliveries.index');
    Route::get('deliveries/{deliveryNote}', [DeliveryNoteController::class, 'show'])->name('deliveries.show');
    Route::get('invoices/{invoice}/return', [CreditNoteController::class, 'create'])->name('credit-notes.create');
    Route::post('invoices/{invoice}/return', [CreditNoteController::class, 'store'])->name('credit-notes.store');
    Route::get('credit-notes', [CreditNoteController::class, 'index'])->name('credit-notes.index');
    Route::get('credit-notes/{creditNote}', [CreditNoteController::class, 'show'])->name('credit-notes.show');

    // ---- Finance ----
    Route::get('receipts', [PaymentController::class, 'index'])->defaults('direction', 'in')->name('receipts.index');
    Route::get('receipts/create', [PaymentController::class, 'create'])->defaults('direction', 'in')->name('receipts.create');
    Route::get('supplier-payments', [PaymentController::class, 'index'])->defaults('direction', 'out')->name('supplier-payments.index');
    Route::get('supplier-payments/create', [PaymentController::class, 'create'])->defaults('direction', 'out')->name('supplier-payments.create');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('cheques', [ChequeController::class, 'index'])->name('cheques.index');
    Route::post('cheques/{cheque}/status', [ChequeController::class, 'status'])->name('cheques.status');
    Route::get('accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::post('accounts', [AccountController::class, 'store'])->name('accounts.store');
    Route::post('accounts/transfer', [AccountController::class, 'transfer'])->name('accounts.transfer');
    Route::get('accounts/{account}', [AccountController::class, 'show'])->name('accounts.show');
    Route::get('cashbook', [AccountController::class, 'cashbook'])->name('cashbook');
    Route::resource('expenses', ExpenseController::class)->only(['index', 'create', 'store']);
    Route::get('credit-control', [CreditControlController::class, 'index'])->name('credit-control.index');
    Route::get('payables', [CreditControlController::class, 'payables'])->name('credit-control.payables');

    // ---- Reports ----
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/stock-valuation', [ReportController::class, 'stockValuation'])->name('reports.stock-valuation');
    Route::get('reports/low-stock', [ReportController::class, 'lowStock'])->name('reports.low-stock');
    Route::get('reports/customer-statement', [ReportController::class, 'statement'])->name('reports.statement');
    Route::get('reports/outstanding', [ReportController::class, 'outstanding'])->name('reports.outstanding');
    Route::get('reports/product-profit', [ReportController::class, 'productProfit'])->name('reports.product-profit');
    Route::get('reports/invoice-profit', [ReportController::class, 'invoiceProfit'])->name('reports.invoice-profit');
    Route::get('reports/cash-flow', [ReportController::class, 'cashFlow'])->name('reports.cash-flow');
    Route::get('reports/expected', [ReportController::class, 'expected'])->name('reports.expected');
    Route::get('reports/import-costing', [ReportController::class, 'importCosting'])->name('reports.import-costing');

    // ---- Admin ----
    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
});
