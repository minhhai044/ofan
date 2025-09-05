<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warranty extends Model
{
    protected $table = 'warranties';

    protected $fillable = [
        'warranty_code',
        'order_item_id',
        'customer_id',
        'product_id',
        'warranty_period',
        'start_date',
        'end_date',
        'status',
        'installation_date',
        'installation_address',
        'product_filter_cores',
    ];

    protected $casts = [
        'product_filter_cores' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'installation_date' => 'date',
    ];

    // Quan hệ
    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function schedules()
    {
        return $this->hasMany(MaintenanceSchedule::class);
    }

    public function tickets()
    {
        return $this->hasMany(WarrantyTicket::class);
    }

    public function maintenanceRecords()
    {
        return $this->hasMany(MaintenanceRecord::class);
    }
}
