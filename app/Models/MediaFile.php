<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'wa_media_id',
        'local_path',
        'file_name',
        'mime_type',
        'file_size',
        'sha256_hash',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
