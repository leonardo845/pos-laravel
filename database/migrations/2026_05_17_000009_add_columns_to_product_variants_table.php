<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('barcode', 100)->nullable()->after('sku');
            $table->decimal('stock', 15, 2)->default(0)->after('barcode');
            $table->boolean('is_stock_tracked')->default(false)->after('stock');
            $table->json('attributes')->nullable()->after('is_stock_tracked');
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['barcode', 'stock', 'is_stock_tracked', 'attributes']);
        });
    }
};
