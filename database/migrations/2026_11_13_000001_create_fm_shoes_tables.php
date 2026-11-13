<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FM Shoes - simple wholesale system (client meeting, Nov 2026).
 *
 *   Buy (purchases) -> stock per product colour -> Sell (invoices) -> payments -> dues & reports
 *
 * A product has a size range (e.g. 30-42), a photo and one or more colours.
 * Stock is counted in pairs per colour. Everything is in LKR. One shop / store.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
        });

        Schema::create('document_sequences', function (Blueprint $table) {
            $table->string('prefix', 10)->primary();
            $table->unsignedInteger('next_number')->default(1);
        });

        // ---------- Products ----------
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('name');
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('size_from', 10)->nullable();
            $table->string('size_to', 10)->nullable();
            $table->string('image_path')->nullable();
            $table->decimal('cost_price', 14, 2)->default(0);     // average cost per pair (updated by purchases)
            $table->decimal('selling_price', 14, 2)->default(0);  // per pair
            $table->unsignedInteger('low_stock_level')->default(12);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_colours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('colour', 40);
            $table->integer('qty')->default(0); // pairs in stock
            $table->timestamps();
            $table->unique(['product_id', 'colour']);
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('product_colour_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20); // opening, purchase, sale, return, purchase_return, correction
            $table->integer('qty');     // + in / - out
            $table->integer('balance'); // stock after this movement
            $table->decimal('unit_cost', 14, 2)->default(0);
            $table->nullableMorphs('reference');
            $table->string('reference_no', 40)->nullable();
            $table->string('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->index(['date', 'type']);
        });

        // ---------- People ----------
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->decimal('opening_balance', 14, 2)->default(0); // we owed them before using the system
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('shop_name')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->decimal('opening_balance', 14, 2)->default(0); // owed to us before using the system
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ---------- Buying ----------
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('number', 30)->unique();
            $table->foreignId('supplier_id')->constrained();
            $table->string('supplier_bill_no', 60)->nullable();
            $table->date('date');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('other_costs', 14, 2)->default(0); // transport etc. - added into the cost of the pairs
            $table->decimal('total', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->decimal('returned_amount', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('product_colour_id')->constrained();
            $table->unsignedInteger('qty');
            $table->decimal('unit_price', 14, 2);  // supplier price per pair
            $table->decimal('line_total', 14, 2);
            $table->decimal('unit_cost', 14, 2);   // price after discount + other costs
            $table->unsignedInteger('returned_qty')->default(0);
            $table->timestamps();
        });

        // ---------- Selling ----------
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number', 30)->unique();
            $table->foreignId('customer_id')->constrained();
            $table->date('date');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->decimal('cost_total', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->decimal('returned_amount', 14, 2)->default(0);
            $table->string('payment_status', 10)->default('unpaid'); // unpaid, partial, paid
            $table->string('share_token', 40)->unique();              // link sent to the customer
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->index(['payment_status', 'date']);
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('product_colour_id')->constrained();
            $table->unsignedInteger('qty');
            $table->decimal('unit_price', 14, 2);
            $table->decimal('line_total', 14, 2);
            $table->decimal('unit_cost', 14, 2)->default(0);
            $table->unsignedInteger('returned_qty')->default(0);
            $table->timestamps();
        });

        // Goods coming back: from a customer (invoice) or going back to a supplier (purchase)
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->string('number', 30)->unique();
            $table->string('type', 10); // customer | supplier
            $table->foreignId('invoice_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->decimal('total', 14, 2)->default(0);
            $table->string('reason')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')->constrained('returns')->cascadeOnDelete();
            $table->foreignId('product_colour_id')->constrained();
            $table->unsignedInteger('qty');
            $table->decimal('unit_price', 14, 2);
            $table->decimal('line_total', 14, 2);
            $table->timestamps();
        });

        // ---------- Money ----------
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('number', 30)->unique();
            $table->string('direction', 3);   // in (from customer) | out (to supplier)
            $table->string('party_type', 10); // customer | supplier
            $table->unsignedBigInteger('party_id');
            $table->date('date');
            $table->decimal('total', 14, 2);
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->index(['party_type', 'party_id']);
        });

        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->string('direction', 10); // received | issued
            $table->string('cheque_no', 30);
            $table->string('bank')->nullable();
            $table->date('cheque_date');
            $table->decimal('amount', 14, 2);
            $table->string('party_type', 10);
            $table->unsignedBigInteger('party_id');
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 12)->default('pending'); // pending, cleared, bounced
            $table->date('status_date')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->string('method', 10); // cash, card, online, cheque
            $table->decimal('amount', 14, 2);
            $table->string('reference')->nullable();
            $table->foreignId('cheque_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        // Which bill a payment settles. Both ids empty = old balance (opening balance)
        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_id')->nullable()->constrained()->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->timestamps();
        });

        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('expense_category_id')->constrained();
            $table->decimal('amount', 14, 2);
            $table->string('method', 10)->default('cash'); // cash, card, online, cheque
            $table->string('paid_to')->nullable();
            $table->string('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['expenses', 'expense_categories', 'payment_allocations', 'payment_lines', 'cheques', 'payments',
            'return_items', 'returns', 'invoice_items', 'invoices', 'purchase_items', 'purchases', 'customers',
            'suppliers', 'stock_movements', 'product_colours', 'products', 'categories', 'brands',
            'document_sequences', 'settings'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
