<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->renameColumn('stock', 'min_stock');
            $table->dropColumn('price');
            $table->decimal('buy_price', 15, 2)->nullable()->after('sku');
            $table->decimal('sell_price', 15, 2)->nullable()->after('buy_price');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->renameColumn('min_stock', 'stock');
            $table->dropColumn(['buy_price', 'sell_price']);
            $table->decimal('price', 15, 2)->nullable()->after('sku');
        });
    }
};
