<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'customer_id',
        'branch_id',
        'salesperson_id',
        'order_date',
        'status',
        'amount',
        'discount_amount',
        'vat_amount',
        'shipping_fee',
        'point',
        'total_amount',
        'payment_method',
        'payment_status',
        'paid_amount',
        'delivery_address',
        'delivery_date',
        'delivery_note',
        'note',
    ];

    protected $casts = [
        'order_date' => 'datetime',
        'delivery_date' => 'datetime',
        'amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'point' => 'integer',
    ];

    protected $attributes = [
        'status' => 0,
        'amount' => 0,
        'discount_amount' => 0,
        'vat_amount' => 0,
        'shipping_fee' => 0,
        'point' => 0,
        'payment_status' => 0,
        'paid_amount' => 0,
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function salesperson()
    {
        return $this->belongsTo(User::class, 'salesperson_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function returns()
    {
        return $this->hasMany(Returns::class);
    }
}