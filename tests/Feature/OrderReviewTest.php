<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\PriceList;
use App\Models\StockSnapshot;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_order_review_page_loads_successfully(): void
    {
        $response = $this->get('/order-review');

        $response->assertStatus(200);
        $response->assertSee('Review Pesanan Obat');
        $response->assertSee('Amoxicillin 500mg Kapsul');
        $response->assertSee('Estimasi Pengiriman: H+1');
    }

    public function test_calculate_order_subtotal_and_total_accurately(): void
    {
        $prod1 = Product::where('kode_produk', 'OBT-001')->first(); // Rp 45.000
        $prod2 = Product::where('kode_produk', 'OBT-002')->first(); // Rp 25.000

        $payload = [
            'items' => [
                ['product_id' => $prod1->id, 'qty' => 2], // 2 x 45.000 = 90.000
                ['product_id' => $prod2->id, 'qty' => 3], // 3 x 25.000 = 75.000
            ],
        ];

        $response = $this->postJson('/api/order/calculate', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'total_items' => 5,
            'total_price' => 165000,
            'formatted_total' => 'Rp 165.000',
        ]);
    }

    public function test_calculate_rejects_empty_items(): void
    {
        $response = $this->postJson('/api/order/calculate', [
            'items' => [],
        ]);

        $response->assertStatus(422);
    }

    public function test_submit_order_creates_po_ticket_and_generates_valid_whatsapp_url(): void
    {
        $prod = Product::where('kode_produk', 'OBT-001')->first();

        $payload = [
            'customer_name' => 'Apotek Sehat Sentosa',
            'customer_phone' => '0895398387040',
            'delivery_address' => 'Jl. Kertajaya Indah No. 12, Surabaya',
            'notes' => 'Tolong kirim pagi',
            'items' => [
                ['product_id' => $prod->id, 'qty' => 2],
            ],
        ];

        $response = $this->postJson('/api/order/submit-wa', $payload);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'ticket_code',
            'order_summary',
            'whatsapp_message',
            'whatsapp_url',
        ]);

        $ticketCode = $response->json('ticket_code');
        $this->assertStringStartsWith('PO-', $ticketCode);

        // Pastikan tiket tersimpan di basis data
        $this->assertDatabaseHas('tickets', [
            'ticket_code' => $ticketCode,
            'type' => 'po',
            'status' => 'baru',
        ]);

        // Pastikan URL WhatsApp valid dan mengarah ke nomor tujuan
        $waUrl = $response->json('whatsapp_url');
        $this->assertStringContainsString('https://wa.me/62895398387040', $waUrl);
        $this->assertStringContainsString(urlencode($ticketCode), $waUrl);
    }

    public function test_submit_order_validates_required_customer_fields(): void
    {
        $response = $this->postJson('/api/order/submit-wa', [
            'customer_name' => '',
            'customer_phone' => '',
            'delivery_address' => '',
            'items' => [],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['customer_name', 'customer_phone', 'delivery_address', 'items']);
    }
}
