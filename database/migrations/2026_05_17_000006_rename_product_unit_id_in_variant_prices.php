<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop FK on product_variant_id (it relies on the unique index we need to remove)
        Schema::table('product_variant_prices', function (Blueprint $table) {
            $table->dropForeign('product_variant_prices_product_variant_id_foreign');
        });
        // Drop unique index and regular index, rename column
        Schema::table('product_variant_prices', function (Blueprint $table) {
            $table->dropUnique('product_variant_prices_product_variant_id_product_unit_id_unique');
            $table->dropIndex('product_variant_prices_product_unit_id_foreign');
            $table->renameColumn('product_unit_id', 'unit_id');
        });
        // Restore indexes and FKs
        Schema::table('product_variant_prices', function (Blueprint $table) {
            $table->unique(['product_variant_id', 'unit_id']);
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->cascadeOnDelete();
            $table->foreign('unit_id')->references('id')->on('units')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('product_variant_prices', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropForeign(['unit_id']);
        });
        Schema::table('product_variant_prices', function (Blueprint $table) {
            $table->dropUnique('product_variant_prices_product_variant_id_unit_id_unique');
            $table->renameColumn('unit_id', 'product_unit_id');
        });
        Schema::table('product_variant_prices', function (Blueprint $table) {
            $table->unique(['product_variant_id', 'product_unit_id']);
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->cascadeOnDelete();
        });
    }
};
