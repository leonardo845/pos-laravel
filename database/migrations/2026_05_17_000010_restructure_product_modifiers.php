<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop old pivot: products <-> modifiers (direct)
        Schema::dropIfExists('product_modifier');

        // New pivot: products <-> modifier_groups
        Schema::create('product_modifier_group', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('modifier_group_id')->constrained('modifier_groups')->cascadeOnDelete();
            $table->primary(['product_id', 'modifier_group_id']);
        });

        // Per-variant modifier prices
        Schema::create('product_variant_modifiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')
                  ->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('modifier_id')
                  ->constrained('modifiers')->cascadeOnDelete();
            $table->decimal('price', 15, 2)->default(0);
            $table->timestamps();
            $table->unique(['product_variant_id', 'modifier_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_modifiers');
        Schema::dropIfExists('product_modifier_group');

        Schema::create('product_modifier', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('modifier_id')->constrained('modifiers')->cascadeOnDelete();
            $table->primary(['product_id', 'modifier_id']);
        });
    }
};
