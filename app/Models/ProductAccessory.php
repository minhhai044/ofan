<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAccessory extends Model
{
    protected $fillable = [
        'product_id',
        'product_accessory_id',
        'quantity',
        'is_active',
    ];

     public function productAccessory()
    {
        return $this->belongsTo(Product::class, 'product_accessory_id');
    }
}
