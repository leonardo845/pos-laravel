<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
            $table->renameColumn('base_price', 'min_price');
            $table->decimal('max_price', 15, 2)->default(0)->after('min_price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->constrained('product_units')->nullOnDelete();
            $table->renameColumn('min_price', 'base_price');
            $table->dropColumn('max_price');
        });
    }
};
