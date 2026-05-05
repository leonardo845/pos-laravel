<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outlet extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'address', 'phone', 'email', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'outlet_products')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    public function outletProducts()
    {
        return $this->hasMany(OutletProduct::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
