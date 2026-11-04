<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'current_state',
        'active_ticket_id',
        'state_payload',
        'last_inbound_at',
    ];

    protected $casts = [
        'state_payload' => 'array',
        'last_inbound_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function activeTicket()
    {
        return $this->belongsTo(Ticket::class, 'active_ticket_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function botEvents()
    {
        return $this->hasMany(BotEvent::class);
    }
}
