<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Client feedback (Oct 2026):
 * - a product is a name with an optional size range (e.g. 28-44) and colour; stock is counted in pairs
 * - everything is in LKR (no foreign currency / exchange rate)
 * - gender is not used
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('size_from', 10)->nullable()->after('colour');
            $table->string('size_to', 10)->nullable()->after('size_from');
            $table->dropColumn('gender');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('size', 30)->change();
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('currency', 3)->default('LKR')->change();
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('currency', 3)->default('LKR')->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('gender', 20)->nullable();
            $table->dropColumn(['size_from', 'size_to']);
        });
    }
};
