<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'wa_number',
        'role',
        'is_active',
        'password',
    ];

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_sales_id');
    }

    public function defaultCustomers()
    {
        return $this->hasMany(Customer::class, 'default_sales_id');
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_sales', 'sales_id', 'customer_id')
            ->withPivot('wilayah')
            ->withTimestamps();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
