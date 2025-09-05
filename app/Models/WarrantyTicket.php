<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarrantyTicket extends Model
{
    protected $table = 'warranty_tickets';

    protected $fillable = [
        'ticket_code',
        'warranty_id',
        'customer_id',
        'issue_category',
        'issue_description',
        'priority',
        'status',
        'assigned_technician_id',
        'notes',
        'created_by',
    ];

    // Quan hệ
    public function warranty()
    {
        return $this->belongsTo(Warranty::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'assigned_technician_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
