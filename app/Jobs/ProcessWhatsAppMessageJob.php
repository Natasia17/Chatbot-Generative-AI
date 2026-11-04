<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\BotEvent;
use App\Services\Ai\SemanticFaqMatcher;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWhatsAppMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function handle(SemanticFaqMatcher $matcher): void
    {
        $messageData = $this->payload['entry'][0]['changes'][0]['value']['messages'][0] ?? null;
        if (!$messageData) {
            return;
        }

        $waMessageId = $messageData['id'];
        $fromNumber = $messageData['from'];
        $type = $messageData['type'];
        $body = $messageData['text']['body'] ?? null;
        $mediaId = $messageData['image']['id'] ?? $messageData['document']['id'] ?? null;

        // 1. Identifikasi Pelanggan Apotek
        $customer = Customer::where('wa_number', $fromNumber)
            ->orWhere('wa_number', 'LIKE', '%' . substr($fromNumber, -9))
            ->first();

        // 2. Cari atau Buat Sesi Percakapan
        $conversation = null;
        if ($customer) {
            $conversation = Conversation::firstOrCreate(
                ['customer_id' => $customer->id],
                ['current_state' => 'IDLE', 'last_inbound_at' => now()]
            );
            $conversation->update(['last_inbound_at' => now()]);
        }

        // 3. Simpan Pesan ke Basis Data (Audit Trail)
        $message = Message::create([
            'wa_message_id' => $waMessageId,
            'conversation_id' => $conversation?->id,
            'sender_role' => $customer ? 'customer' : 'unknown',
            'direction' => 'inbound',
            'message_type' => $type,
            'body' => $body,
            'media_id' => $mediaId,
            'raw_payload' => $messageData,
        ]);

        // 4. Jika pesan berupa teks dari apotek, proses pencocokan FAQ
        if ($type === 'text' && !empty($body)) {
            $startTime = microtime(true);
            $matchResult = $matcher->match($body);
            $durationMs = (int) round((microtime(true) - $startTime) * 1000);

            // Log event analitik untuk evaluasi TA bab 4
            BotEvent::create([
                'conversation_id' => $conversation?->id,
                'raw_query' => $body,
                'intent_detected' => $matchResult['faq']?->kategori ?? 'Unknown',
                'confidence_score' => $matchResult['confidence'],
                'matched_faq_id' => $matchResult['faq']?->id,
                'outcome' => $matchResult['status'],
                'response_time_ms' => $durationMs,
            ]);

            Log::info("Bot matched query '{$body}' with status: {$matchResult['status']} (score: {$matchResult['confidence']})");
        }
    }
}
