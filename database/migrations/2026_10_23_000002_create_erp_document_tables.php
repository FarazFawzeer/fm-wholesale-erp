<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Transactions: purchasing/import, inventory, sales, payments, cheques, expenses.
     */
    public function up(): void
    {
        // ---- Purchasing & import ----
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('number', 30)->unique();
            $table->foreignId('supplier_id')->constrained();
            $table->date('order_date');
            $table->date('expected_date')->nullable();
            $table->string('currency', 3)->default('USD');
            $table->decimal('exchange_rate', 12, 4)->default(1);
            $table->string('allocation_method', 10)->default('value'); // value | qty
            $table->string('shipment_ref')->nullable();
            $table->string('status', 20)->default('draft'); // draft, ordered, costed, partial, received, cancelled
            $table->decimal('subtotal_fx', 14, 2)->default(0);
            $table->decimal('charges_total', 14, 2)->default(0);
            $table->decimal('landed_total', 14, 2)->default(0);
            $table->decimal('payable_amount', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('product_variant_id')->constrained();
            $table->unsignedInteger('qty');
            $table->decimal('unit_price_fx', 14, 4);
            $table->decimal('landed_unit_cost', 14, 2)->default(0);
            $table->unsignedInteger('received_qty')->default(0);
            $table->timestamps();
        });

        Schema::create('purchase_order_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30); // freight, insurance, duty, clearing, transport, other
            $table->string('description')->nullable();
            $table->decimal('amount', 14, 2);
            $table->timestamps();
        });

        Schema::create('grns', function (Blueprint $table) {
            $table->id();
            $table->string('number', 30)->unique();
            $table->foreignId('purchase_order_id')->constrained();
            $table->foreignId('supplier_id')->constrained();
            $table->foreignId('warehouse_id')->constrained();
            $table->date('received_date');
            $table->string('status', 20)->default('draft'); // draft, posted
            $table->decimal('total_value', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('grn_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grn_id')->constrained()->cascadeOnDelete();
            $table->foreignId('purchase_order_item_id')->constrained();
            $table->foreignId('product_variant_id')->constrained();
            $table->unsignedInteger('ordered_qty');
            $table->unsignedInteger('received_qty')->default(0);
            $table->unsignedInteger('damaged_qty')->default(0);
            $table->decimal('unit_cost', 14, 2)->default(0);
            $table->timestamps();
        });

        // ---- Inventory ----
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('number', 30)->unique();
            $table->foreignId('from_warehouse_id')->constrained('warehouses');
            $table->foreignId('to_warehouse_id')->constrained('warehouses');
            $table->date('date');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('number', 30)->unique();
            $table->foreignId('warehouse_id')->constrained();
            $table->date('date');
            $table->string('reason', 30); // count, damaged, lost, found, supplier_return, damaged_writeoff
            $table->text('notes')->nullable();
            $table->decimal('value', 14, 2)->default(0);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('stock_document_items', function (Blueprint $table) {
            $table->id();
            $table->morphs('document');
            $table->foreignId('product_variant_id')->constrained();
            $table->integer('qty');
            $table->decimal('unit_cost', 14, 2)->default(0);
            $table->timestamps();
        });

        // ---- Sales ----
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->id();
            $table->string('type', 12); // quotation | order
            $table->string('number', 30)->unique();
            $table->foreignId('customer_id')->constrained();
            $table->date('date');
            $table->date('valid_until')->nullable();
            $table->string('status', 20)->default('open'); // open, accepted, rejected, converted, invoiced
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->foreignId('converted_from_id')->nullable()->constrained('sales_orders')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number', 30)->unique();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('sales_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->constrained();
            $table->date('date');
            $table->date('due_date');
            $table->string('sale_type', 10)->default('credit'); // cash | credit
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('discount_amount', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->decimal('cost_total', 14, 2)->default(0);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->decimal('credited_amount', 14, 2)->default(0);
            $table->string('payment_status', 12)->default('unpaid'); // unpaid, partial, paid
            $table->string('delivery_status', 12)->default('pending'); // pending, partial, delivered
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('sales_lines', function (Blueprint $table) {
            $table->id();
            $table->morphs('document'); // sales_orders | invoices
            $table->foreignId('product_id')->constrained();
            $table->foreignId('product_variant_id')->constrained();
            $table->unsignedInteger('qty');
            $table->decimal('unit_price', 14, 2);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('line_total', 14, 2);
            $table->decimal('unit_cost', 14, 2)->default(0);
            $table->unsignedInteger('delivered_qty')->default(0);
            $table->unsignedInteger('returned_qty')->default(0);
            $table->timestamps();
        });

        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();
            $table->string('number', 30)->unique();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('warehouse_id')->constrained();
            $table->date('date');
            $table->string('vehicle_no')->nullable();
            $table->string('received_by')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('delivery_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_note_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sales_line_id')->constrained();
            $table->foreignId('product_variant_id')->constrained();
            $table->unsignedInteger('qty');
            $table->timestamps();
        });

        Schema::create('credit_notes', function (Blueprint $table) {
            $table->id();
            $table->string('number', 30)->unique();
            $table->foreignId('invoice_id')->constrained();
            $table->foreignId('customer_id')->constrained();
            $table->date('date');
            $table->string('reason')->nullable();
            $table->boolean('restock')->default(true);
            $table->decimal('total', 14, 2)->default(0);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('credit_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credit_note_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sales_line_id')->constrained();
            $table->foreignId('product_variant_id')->constrained();
            $table->unsignedInteger('qty');
            $table->decimal('unit_price', 14, 2);
            $table->decimal('line_total', 14, 2);
            $table->timestamps();
        });

        // ---- Money ----
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 10); // cash | bank | card
            $table->string('bank_name')->nullable();
            $table->string('account_no')->nullable();
            $table->decimal('opening_balance', 14, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('number', 30)->unique();
            $table->string('direction', 3); // in | out
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
            $table->string('branch')->nullable();
            $table->date('cheque_date');
            $table->decimal('amount', 14, 2);
            $table->string('party_type', 10);
            $table->unsignedBigInteger('party_id');
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 12)->default('pending'); // pending, deposited, cleared, bounced, cancelled
            $table->date('status_date')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->string('method', 15); // cash, bank_transfer, card, cheque
            $table->foreignId('account_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cheque_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 14, 2);
            $table->string('reference')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained()->nullOnDelete();
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
            $table->string('number', 30)->unique();
            $table->date('date');
            $table->foreignId('expense_category_id')->constrained();
            $table->foreignId('account_id')->constrained();
            $table->decimal('amount', 14, 2);
            $table->string('payee')->nullable();
            $table->string('description')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('account_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('type', 30);
            $table->string('description');
            $table->decimal('amount', 14, 2); // + money in, - money out
            $table->nullableMorphs('reference');
            $table->timestamps();
            $table->index(['account_id', 'date']);
        });
    }

    public function down(): void
    {
        foreach (['account_transactions', 'expenses', 'expense_categories', 'payment_allocations', 'payment_lines',
            'cheques', 'payments', 'accounts', 'credit_note_items', 'credit_notes', 'delivery_note_items',
            'delivery_notes', 'sales_lines', 'invoices', 'sales_orders', 'stock_document_items', 'stock_adjustments',
            'stock_transfers', 'grn_items', 'grns', 'purchase_order_charges', 'purchase_order_items', 'purchase_orders'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
