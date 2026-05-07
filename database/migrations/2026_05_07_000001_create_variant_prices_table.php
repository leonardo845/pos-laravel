<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variant_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('product_units')->cascadeOnDelete();
            $table->decimal('price', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['variant_id', 'unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variant_prices');
    }
};
