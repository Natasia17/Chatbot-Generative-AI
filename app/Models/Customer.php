<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_apotek',
        'nama_apotek',
        'wa_number',
        'alamat',
        'default_sales_id',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function defaultSales()
    {
        return $this->belongsTo(User::class, 'default_sales_id');
    }

    public function salesTeam()
    {
        return $this->belongsToMany(User::class, 'customer_sales', 'customer_id', 'sales_id')
            ->withPivot('wilayah')
            ->withTimestamps();
    }

    public function conversation()
    {
        return $this->hasOne(Conversation::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
