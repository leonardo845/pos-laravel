<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModifierGroup extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'min_selection'];

    protected $casts = [
        'min_selection' => 'integer',
    ];

    public function modifiers()
    {
        return $this->hasMany(Modifier::class);
    }
}
