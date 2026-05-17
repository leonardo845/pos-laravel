<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantModifier extends Model
{
    protected $fillable = ['product_variant_id', 'modifier_id', 'price'];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function modifier()
    {
        return $this->belongsTo(Modifier::class);
    }
}
