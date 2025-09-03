<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_category_id',
        'name',
        'sku',
        'code_misa',
        'bar_code',
        'slug',
        'commission_discount',
        'price',
        'price_sale',
        'filter_stages',
        'unit',
        'is_active',
        'is_special',
        'images',
        'description'
    ];

    protected $casts = [
        'images'          => 'array',
        'is_active'       => 'boolean',
        'is_special'      => 'boolean',
        'price'           => 'decimal:0',
        'price_sale'      => 'decimal:0',
        'commission_discount' => 'float',
        'filter_stages'   => 'integer',
    ];

    public function productFilters()
    {
        return $this->hasMany(ProductFilter::class, 'product_id');
    }

    public function productAccessories()
    {
        return $this->hasMany(ProductAccessory::class, 'product_id');
    }
}
