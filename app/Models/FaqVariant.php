<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FaqVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'faq_id',
        'variant_text',
    ];

    public function faq()
    {
        return $this->belongsTo(Faq::class);
    }
}
