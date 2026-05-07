<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariantPrice extends Model
{
    protected $fillable = [
        'variant_id',
        'unit_id',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function unit()
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id');
    }
}
