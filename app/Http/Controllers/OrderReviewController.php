<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Ticket;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class OrderReviewController extends Controller
{
    /**
     * Tampilkan halaman mobile web ringkasan pesanan & harga obat
     */
    public function index()
    {
        $products = Product::with(['latestPrice', 'latestStock'])
            ->where('is_active', true)
            ->get();

        $defaultCustomer = Customer::first();
        // Nomor WhatsApp tujuan apotek (nomor uji baru atau default sistem)
        $apotekWa = env('WHATSAPP_TEST_NUMBER', '62895398387040');

        return view('order.review', compact('products', 'defaultCustomer', 'apotekWa'));
    }

    /**
     * Endpoint API untuk menghitung subtotal dan total harga secara aman di server
     */
    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1|max:500',
        ]);

        $calculation = $this->calculateOrderDetails($validated['items']);

        return response()->json([
            'success' => true,
            'items' => $calculation['items'],
            'total_items' => $calculation['total_items'],
            'total_price' => $calculation['total_price'],
            'formatted_total' => 'Rp ' . number_format($calculation['total_price'], 0, ',', '.'),
        ]);
    }

    /**
     * Submit pesanan dan buat link resmi WhatsApp siap kirim
     */
    public function submitToWhatsApp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'delivery_address' => 'required|string|max:255',
            'notes' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1|max:500',
        ]);

        $calculation = $this->calculateOrderDetails($validated['items']);

        if (empty($calculation['items'])) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada produk yang valid untuk dipesan.',
            ], 422);
        }

        // Generate Ticket PO di sistem (FR-06)
        $ticketCode = 'PO-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $cleanPhone = preg_replace('/[^0-9]/', '', $validated['customer_phone']);
        $customer = Customer::where('wa_number', 'LIKE', '%' . substr($cleanPhone, -9))->first();
        if (!$customer) {
            $customer = Customer::create([
                'kode_apotek' => 'CUST-' . strtoupper(Str::random(4)),
                'nama_apotek' => $validated['customer_name'],
                'wa_number' => $cleanPhone,
                'alamat' => $validated['delivery_address'],
            ]);
        }

        $ticket = Ticket::create([
            'ticket_code' => $ticketCode,
            'type' => 'po',
            'customer_id' => $customer->id,
            'status' => 'baru',
            'sla_due_at' => now()->addMinutes(30),
        ]);

        // Draf pesan WhatsApp (Humanizer: ramah, terstruktur, empati seperti staf apotek)
        $pesanWa = $this->composeHumanizedWhatsAppMessage(
            $validated['customer_name'],
            $validated['customer_phone'],
            $validated['delivery_address'],
            $validated['notes'] ?? '-',
            $ticketCode,
            $calculation
        );

        $targetWa = env('WHATSAPP_TEST_NUMBER', '62895398387040');
        $cleanTargetWa = preg_replace('/[^0-9]/', '', $targetWa);
        if (str_starts_with($cleanTargetWa, '0')) {
            $cleanTargetWa = '62' . substr($cleanTargetWa, 1);
        }

        $waUrl = 'https://wa.me/' . $cleanTargetWa . '?text=' . urlencode($pesanWa);

        return response()->json([
            'success' => true,
            'ticket_code' => $ticketCode,
            'order_summary' => $calculation,
            'whatsapp_message' => $pesanWa,
            'whatsapp_url' => $waUrl,
        ]);
    }

    /**
     * Hitung detail pesanan secara aman dari harga resmi di database
     */
    private function calculateOrderDetails(array $rawItems): array
    {
        $calculatedItems = [];
        $totalPrice = 0;
        $totalItems = 0;

        foreach ($rawItems as $item) {
            $product = Product::with(['latestPrice', 'latestStock'])->find($item['product_id']);
            if (!$product || !$product->is_active) {
                continue;
            }

            $unitPrice = (float) ($product->latestPrice?->harga ?? 0);
            $qty = (int) $item['qty'];
            $subtotal = $unitPrice * $qty;

            $calculatedItems[] = [
                'product_id' => $product->id,
                'kode_produk' => $product->kode_produk,
                'nama_produk' => $product->nama_produk,
                'satuan' => $product->satuan,
                'unit_price' => $unitPrice,
                'formatted_price' => 'Rp ' . number_format($unitPrice, 0, ',', '.'),
                'qty' => $qty,
                'subtotal' => $subtotal,
                'formatted_subtotal' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
                'stock_status' => $product->latestStock?->status ?? 'Tersedia',
            ];

            $totalPrice += $subtotal;
            $totalItems += $qty;
        }

        return [
            'items' => $calculatedItems,
            'total_items' => $totalItems,
            'total_price' => $totalPrice,
        ];
    }

    /**
     * Susun teks WhatsApp yang ramah dan alami (Humanizer Standard)
     */
    private function composeHumanizedWhatsAppMessage(
        string $name,
        string $phone,
        string $address,
        string $notes,
        string $ticketCode,
        array $calc
    ): string {
        $lines = [];
        $lines[] = "Halo tim layanan Apotek, saya ingin mengonfirmasi pesanan obat dengan rincian berikut:";
        $lines[] = "";
        $lines[] = "*ID Pesanan:* {$ticketCode}";
        $lines[] = "*Nama Pemesan:* {$name}";
        $lines[] = "*Nomor Kontak:* {$phone}";
        $lines[] = "*Alamat Pengiriman:* {$address}";
        if ($notes !== '-') {
            $lines[] = "*Catatan:* {$notes}";
        }
        $lines[] = "";
        $lines[] = "*Daftar Obat yang Dipesan:*";

        $no = 1;
        foreach ($calc['items'] as $item) {
            $lines[] = "{$no}. {$item['nama_produk']} ({$item['qty']} {$item['satuan']}) - {$item['formatted_subtotal']}";
            $no++;
        }

        $lines[] = "";
        $lines[] = "*Estimasi Total Biaya:* Rp " . number_format($calc['total_price'], 0, ',', '.');
        $lines[] = "*(Pengiriman dilakukan H+1 sesuai antrean apotek)*";
        $lines[] = "";
        $lines[] = "Mohon dicek kembali ketersediaan stok fisik dan info pembayarannya. Terima kasih banyak.";

        return implode("\n", $lines);
    }
}
