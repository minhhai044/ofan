<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'address',
        'code_misa',
        'is_active',
        'branch_id',
        'slug'
    ];
}
