<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Returns extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'customer_id',
        'status',
        'reason',
        'note',
        'refund_amount',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
    ];

    protected $attributes = [
        'status' => 0,
        'refund_amount' => 0,
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    public function returnItems()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }
}