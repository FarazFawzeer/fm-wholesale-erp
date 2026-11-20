<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** One item no can come in several size ranges (e.g. 5-7, 11-1, 2-5) - each colour line now has its own size range. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_colours', function (Blueprint $table) {
            $table->string('size', 20)->nullable()->after('colour');
            $table->unique(['product_id', 'colour', 'size'], 'product_colours_product_colour_size_unique');
            $table->dropUnique(['product_id', 'colour']);
        });
    }

    public function down(): void
    {
        Schema::table('product_colours', function (Blueprint $table) {
            $table->dropUnique('product_colours_product_colour_size_unique');
            $table->dropColumn('size');
            $table->unique(['product_id', 'colour']);
        });
    }
};
