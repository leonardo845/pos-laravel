<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('product_units', 'units');
    }

    public function down(): void
    {
        Schema::rename('units', 'product_units');
    }
};
