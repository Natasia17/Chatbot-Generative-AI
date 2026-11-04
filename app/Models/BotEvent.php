<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'raw_query',
        'intent_detected',
        'confidence_score',
        'matched_faq_id',
        'outcome',
        'response_time_ms',
    ];

    protected $casts = [
        'confidence_score' => 'float',
        'response_time_ms' => 'integer',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function matchedFaq()
    {
        return $this->belongsTo(Faq::class, 'matched_faq_id');
    }
}
