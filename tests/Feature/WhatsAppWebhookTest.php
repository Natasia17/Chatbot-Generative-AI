<?php

namespace Tests\Feature;

use App\Jobs\ProcessWhatsAppMessageJob;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class WhatsAppWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_webhook_verification_succeeds_with_valid_token(): void
    {
        $response = $this->get('/api/webhook/whatsapp?' . http_build_query([
            'hub_mode' => 'subscribe',
            'hub_verify_token' => 'secret_apotek_ta_2026',
            'hub_challenge' => '123456789',
        ]));

        $response->assertStatus(200);
        $this->assertEquals('123456789', $response->getContent());
    }

    public function test_webhook_verification_fails_with_invalid_token(): void
    {
        $response = $this->get('/api/webhook/whatsapp?' . http_build_query([
            'hub_mode' => 'subscribe',
            'hub_verify_token' => 'token_palsu_999',
            'hub_challenge' => '123456789',
        ]));

        $response->assertStatus(403);
    }

    public function test_handles_inbound_whatsapp_message_and_dispatches_job(): void
    {
        Queue::fake();

        $payload = [
            'object' => 'whatsapp_business_account',
            'entry' => [
                [
                    'id' => 'WHATSAPP_BUSINESS_ACCOUNT_ID',
                    'changes' => [
                        [
                            'value' => [
                                'messaging_product' => 'whatsapp',
                                'metadata' => [
                                    'display_phone_number' => '15550001234',
                                    'phone_number_id' => '1000000001',
                                ],
                                'contacts' => [
                                    [
                                        'profile' => ['name' => 'Apotek Sehat'],
                                        'wa_id' => '6281234567890',
                                    ],
                                ],
                                'messages' => [
                                    [
                                        'from' => '6281234567890',
                                        'id' => 'wamid.HBgLMjAyNjExMDQwMDAxAA==',
                                        'timestamp' => '1730728800',
                                        'text' => [
                                            'body' => 'Kapan pesanan obat saya dikirim ya?',
                                        ],
                                        'type' => 'text',
                                    ],
                                ],
                            ],
                            'field' => 'messages',
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/webhook/whatsapp', $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'received']);

        Queue::assertPushed(ProcessWhatsAppMessageJob::class);
    }

    public function test_ignores_duplicate_whatsapp_message(): void
    {
        Queue::fake();

        // Buat record pesan yang sudah pernah diterima
        Message::create([
            'wa_message_id' => 'wamid.DUPLICATE_TEST_001',
            'direction' => 'inbound',
            'message_type' => 'text',
            'body' => 'Pesan pertama',
        ]);

        $payload = [
            'entry' => [
                [
                    'changes' => [
                        [
                            'value' => [
                                'messages' => [
                                    [
                                        'from' => '6281234567890',
                                        'id' => 'wamid.DUPLICATE_TEST_001',
                                        'type' => 'text',
                                        'text' => ['body' => 'Pesan duplikat'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/webhook/whatsapp', $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'duplicate_ignored']);

        // Pastikan job TIDAK dipanggil ulang
        Queue::assertNotPushed(ProcessWhatsAppMessageJob::class);
    }
}
