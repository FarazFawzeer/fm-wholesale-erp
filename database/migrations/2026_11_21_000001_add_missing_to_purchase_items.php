<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Pairs paid for but not received (damaged, kept by customs, missing) on each purchase line. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->unsignedInteger('missing_qty')->default(0)->after('qty');
            $table->string('missing_reason', 60)->nullable()->after('missing_qty');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn(['missing_qty', 'missing_reason']);
        });
    }
};
