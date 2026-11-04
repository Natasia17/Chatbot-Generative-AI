<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_code',
        'type',
        'customer_id',
        'assigned_sales_id',
        'status',
        'sla_due_at',
        'escalated_at',
        'closed_at',
    ];

    protected $casts = [
        'sla_due_at' => 'datetime',
        'escalated_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedSales()
    {
        return $this->belongsTo(User::class, 'assigned_sales_id');
    }

    public function returnRequest()
    {
        return $this->hasOne(ReturnRequest::class);
    }

    public function mediaFiles()
    {
        return $this->hasMany(MediaFile::class);
    }
}
