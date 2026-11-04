<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessWhatsAppMessageJob;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * Verifikasi Webhook Meta (GET)
     * Digunakan oleh Meta Developers saat pertama kali mendaftarkan webhook.
     */
    public function verify(Request $request): Response
    {
        $mode = $request->query('hub_mode') ?? $request->query('hub.mode');
        $token = $request->query('hub_verify_token') ?? $request->query('hub.verify_token');
        $challenge = $request->query('hub_challenge') ?? $request->query('hub.challenge');

        $expectedToken = config('services.whatsapp.verify_token', env('WHATSAPP_VERIFY_TOKEN', 'secret_apotek_ta_2026'));

        if ($mode === 'subscribe' && $token === $expectedToken) {
            Log::info('WhatsApp Webhook successfully verified by Meta.');
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        Log::warning('WhatsApp Webhook verification failed. Invalid token received.', [
            'mode' => $mode,
            'token' => $token,
        ]);

        return response('Forbidden', 403);
    }

    /**
     * Terima Pesan Masuk dari Meta Cloud API (POST)
     */
    public function handle(Request $request): JsonResponse
    {
        // 1. Verifikasi Signature X-Hub-Signature-256 (jika diaktifkan)
        $appSecret = config('services.whatsapp.app_secret', env('WHATSAPP_APP_SECRET'));
        if (!empty($appSecret)) {
            $signatureHeader = $request->header('X-Hub-Signature-256');
            if (!$signatureHeader || !str_starts_with($signatureHeader, 'sha256=')) {
                return response()->json(['error' => 'Missing or invalid signature header'], 401);
            }

            $signature = substr($signatureHeader, 7);
            $expectedSignature = hash_hmac('sha256', $request->getContent(), $appSecret);

            if (!hash_equals($expectedSignature, $signature)) {
                Log::error('WhatsApp Webhook signature mismatch.');
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        $payload = $request->all();

        // 2. Deteksi Pesan Masuk
        $messages = $payload['entry'][0]['changes'][0]['value']['messages'] ?? null;
        if (!empty($messages)) {
            $firstMessage = $messages[0];
            $waMessageId = $firstMessage['id'] ?? null;

            // 3. Pengecekan Idempotensi (Mencegah duplikasi retry Meta)
            if ($waMessageId && Message::where('wa_message_id', $waMessageId)->exists()) {
                Log::info("Ignored duplicate WhatsApp message: {$waMessageId}");
                return response()->json(['status' => 'duplicate_ignored'], 200);
            }

            // 4. Lempar ke Database Queue Worker
            ProcessWhatsAppMessageJob::dispatch($payload);
        }

        // 5. Wajib balas HTTP 200 cepat < 1 detik ke Meta
        return response()->json(['status' => 'received'], 200);
    }
}
