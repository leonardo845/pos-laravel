<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function prices()
    {
        return $this->hasMany(ProductVariantPrice::class, 'product_variant_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'variant_id');
    }
}
