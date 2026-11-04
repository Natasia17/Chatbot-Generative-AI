<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'satuan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function latestStock()
    {
        return $this->hasOne(StockSnapshot::class)->latestOfMany();
    }

    public function stocks()
    {
        return $this->hasMany(StockSnapshot::class);
    }

    public function latestPrice()
    {
        return $this->hasOne(PriceList::class)->latestOfMany();
    }

    public function prices()
    {
        return $this->hasMany(PriceList::class);
    }
}
