<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_product_unit', function (Blueprint $table) {
            $table->renameColumn('product_unit_id', 'unit_id');
        });

        Schema::rename('product_product_unit', 'product_unit');
    }

    public function down(): void
    {
        Schema::rename('product_unit', 'product_product_unit');

        Schema::table('product_product_unit', function (Blueprint $table) {
            $table->renameColumn('unit_id', 'product_unit_id');
        });
    }
};
