<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Many-to-many pivot table between products and product_units
        Schema::create('product_product_unit', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('product_unit_id')->constrained('product_units')->cascadeOnDelete();
            $table->primary(['product_id', 'product_unit_id']);
        });

        // Remove buy_price, sell_price, min_stock from product_variants
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['buy_price', 'sell_price', 'min_stock']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_product_unit');

        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('buy_price', 15, 2)->nullable()->after('sku');
            $table->decimal('sell_price', 15, 2)->nullable()->after('buy_price');
            $table->integer('min_stock')->default(0)->after('sell_price');
        });
    }
};
