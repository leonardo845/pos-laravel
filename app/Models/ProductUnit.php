<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductUnit extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_product_unit', 'product_unit_id', 'product_id');
    }
}
