<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantPrice extends Model
{
    protected $fillable = [
        'product_variant_id',
        'product_unit_id',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function unit()
    {
        return $this->belongsTo(ProductUnit::class, 'product_unit_id');
    }
}
