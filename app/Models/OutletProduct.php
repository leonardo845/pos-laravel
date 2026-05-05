<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutletProduct extends Model
{
    use SoftDeletes;

    protected $fillable = ['outlet_id', 'product_id', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
