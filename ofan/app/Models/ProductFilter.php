<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFilter extends Model
{
    protected $fillable = [
        'product_id',
        'product_filter_id',
        'maintenance_schedule',
        'quantity',
        'is_active',
    ];
}
