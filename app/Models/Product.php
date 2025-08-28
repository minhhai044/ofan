<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'code',
        'code_misa',
        'slug',
        'price',
        'price_sale',
        'is_active',
        'images',
        'maintenance_schedule',
        'description',
    ];

    protected $casts = [
        'images' => 'array',
        'price' => 'decimal:0',
        'price_sale' => 'decimal:0',
    ];
}
