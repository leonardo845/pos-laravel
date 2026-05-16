<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'description',
        'min_price',
        'is_active',
    ];

    protected $casts = [
        'min_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function units()
    {
        return $this->belongsToMany(ProductUnit::class, 'product_product_unit', 'product_id', 'product_unit_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function outlets()
    {
        return $this->belongsToMany(Outlet::class, 'outlet_products')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    public function outletProducts()
    {
        return $this->hasMany(OutletProduct::class);
    }
}
