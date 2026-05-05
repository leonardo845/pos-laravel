<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'unit_id',
        'code',
        'name',
        'description',
        'buy_price',
        'sell_price',
        'is_active',
    ];

    protected $casts = [
        'buy_price'  => 'decimal:2',
        'sell_price' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id');
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
