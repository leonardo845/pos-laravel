<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variant_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('product_unit_id')->constrained('product_units')->cascadeOnDelete();
            $table->decimal('price', 15, 2)->default(0);
            $table->timestamps();
            $table->unique(['product_variant_id', 'product_unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_prices');
    }
};
