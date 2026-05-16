<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone_number',
        'email',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
}
