<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    protected $table = 'maintenance_records';

    protected $fillable = [
        'maintenance_schedule_id',
        'warranty_id',
        'technician_id',
        'work_description',
        'parts_replaced',
        'cost_amount',
        'files',
        'status',
        'next_maintenance_date',
    ];

    protected $casts = [
        'parts_replaced' => 'array',
        'files' => 'array',
        'next_maintenance_date' => 'date',
        'cost_amount' => 'decimal:2',
    ];

    // Quan hệ
    public function schedule()
    {
        return $this->belongsTo(MaintenanceSchedule::class, 'maintenance_schedule_id');
    }

    public function warranty()
    {
        return $this->belongsTo(Warranty::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
