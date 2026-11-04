<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'harga',
        'berlaku_dari',
        'berlaku_sampai',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'berlaku_dari' => 'date',
        'berlaku_sampai' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
