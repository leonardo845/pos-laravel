<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'variant_id',
        'transaction_type',
        'reference_id',
        'qty',
        'notes',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    const TRANSACTION_TYPES = [
        'sale',
        'purchase',
        'initial',
        'adjustment',
        'sale_return',
        'purchase_return',
        'stock_opname',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
