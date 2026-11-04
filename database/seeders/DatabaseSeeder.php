<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now('Asia/Jakarta');

        // 1. SEED USERS (Admin, Manager, Sales)
        $adminId = DB::table('users')->insertGetId([
            'name' => 'Admin Utama',
            'email' => 'admin@apotek-distributor.id',
            'wa_number' => '628111000111',
            'role' => 'admin',
            'is_active' => true,
            'password' => Hash::make('password123'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $managerId = DB::table('users')->insertGetId([
            'name' => 'Budi Santoso (Sales Manager)',
            'email' => 'manager@apotek-distributor.id',
            'wa_number' => '628111000222',
            'role' => 'manager',
            'is_active' => true,
            'password' => Hash::make('password123'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $sales1Id = DB::table('users')->insertGetId([
            'name' => 'Andi Wijaya (Sales Surabaya Timur)',
            'email' => 'andi.sales@apotek-distributor.id',
            'wa_number' => '628111000333',
            'role' => 'sales',
            'is_active' => true,
            'password' => Hash::make('password123'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $sales2Id = DB::table('users')->insertGetId([
            'name' => 'Citra Lestari (Sales Surabaya Barat)',
            'email' => 'citra.sales@apotek-distributor.id',
            'wa_number' => '628111000444',
            'role' => 'sales',
            'is_active' => true,
            'password' => Hash::make('password123'),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. SEED CUSTOMERS (Apotek)
        $apt1Id = DB::table('customers')->insertGetId([
            'kode_apotek' => 'APT-SBY-001',
            'nama_apotek' => 'Apotek Sehat Sentosa',
            'wa_number' => '6281234567890',
            'alamat' => 'Jl. Manyar Kertoarjo No. 45, Surabaya Timur',
            'default_sales_id' => $sales1Id,
            'verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $apt2Id = DB::table('customers')->insertGetId([
            'kode_apotek' => 'APT-SBY-002',
            'nama_apotek' => 'Apotek K-24 Rungkut Asri',
            'wa_number' => '6281234567891',
            'alamat' => 'Jl. Rungkut Asri Timur No. 12, Surabaya Timur',
            'default_sales_id' => $sales1Id,
            'verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $apt3Id = DB::table('customers')->insertGetId([
            'kode_apotek' => 'APT-SBY-003',
            'nama_apotek' => 'Apotek Kimia Farma Mayjen Sungkono',
            'wa_number' => '6281234567892',
            'alamat' => 'Jl. Mayjen Sungkono No. 88, Surabaya Barat',
            'default_sales_id' => $sales2Id,
            'verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Customer Sales Mapping
        DB::table('customer_sales')->insert([
            ['customer_id' => $apt1Id, 'sales_id' => $sales1Id, 'wilayah' => 'Surabaya Timur', 'created_at' => $now, 'updated_at' => $now],
            ['customer_id' => $apt2Id, 'sales_id' => $sales1Id, 'wilayah' => 'Surabaya Timur', 'created_at' => $now, 'updated_at' => $now],
            ['customer_id' => $apt3Id, 'sales_id' => $sales2Id, 'wilayah' => 'Surabaya Barat', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 3. SEED 5 OFFICIAL FAQS & VARIANTS (from PROJEK.docx)
        $faqData = [
            [
                'kategori' => 'Pengiriman',
                'question' => 'Kapan pesanan obat/barang dikirim?',
                'answer' => 'Pengiriman dilakukan H+1 setelah orderan dibuat atau diterima oleh sales kami.',
                'variants' => [
                    'kapan barang dikirim?',
                    'jadwal kirim orderan kapan?',
                    'apakah bisa kirim hari ini?',
                    'estimasi pengiriman berapa hari?',
                ]
            ],
            [
                'kategori' => 'Pengiriman',
                'question' => 'Jam berapa pengiriman tiba di apotek?',
                'answer' => 'Pengiriman pada hari H dilakukan sesuai dengan rute alamat customer, jadi mohon bersabar menunggu driver tiba di lokasi.',
                'variants' => [
                    'kok belum sampai barangnya?',
                    'jam berapa driver sampai ke apotek?',
                    'rute pengiriman hari ini jam berapa?',
                    'bisa minta no kurir atau posisi barang?',
                ]
            ],
            [
                'kategori' => 'Retur & Kendala',
                'question' => 'Bagaimana jika kemasan obat rusak atau barang tidak sesuai saat diterima?',
                'answer' => 'Kemasan yang diterima dipastikan sesuai dan dalam keadaan bagus dari gudang. Jika ada kendala (rusak/bocor/salah item), silakan langsung pilih menu Ajukan Retur dan kirimkan foto sebagai bukti checking ulang sebelum proses retur barang diproses.',
                'variants' => [
                    'ada obat yang pecah atau bocor',
                    'barang yang dikirim salah item',
                    'kemasan rusak penyok saat kurir antar',
                    'gimana cara klaim retur barang rusak?',
                ]
            ],
            [
                'kategori' => 'Kunjungan Sales',
                'question' => 'Mengapa sales yang datang visit meminta tanda tangan atau foto apotek?',
                'answer' => 'Sales visit ke apotek wajib memberikan bukti kunjungan (tanda tangan atau foto) guna melengkapi verifikasi bukti visit mereka di sistem perusahaan.',
                'variants' => [
                    'kenapa sales minta foto pas ke apotek?',
                    'sales wajib ttd visit ya?',
                    'bukti kunjungan sales untuk apa?',
                    'apoteker tidak mau difoto waktu sales datang',
                ]
            ],
            [
                'kategori' => 'Product Knowledge',
                'question' => 'Kapan apotek bisa meminta presentasi atau product knowledge obat baru?',
                'answer' => 'Permintaan product knowledge ke apotek dapat diajukan setidaknya 1 bulan sekali, dengan rentang rotasi 5–6 bulan sekali untuk kembali ke apotek yang sama.',
                'variants' => [
                    'bisa minta edukasi produk obat baru?',
                    'kapan sales bisa presentasi product knowledge?',
                    'jadwal update info obat dari principal kapan?',
                ]
            ],
        ];

        foreach ($faqData as $f) {
            $faqId = DB::table('faqs')->insertGetId([
                'kategori' => $f['kategori'],
                'canonical_question' => $f['question'],
                'answer' => $f['answer'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($f['variants'] as $v) {
                DB::table('faq_variants')->insert([
                    'faq_id' => $faqId,
                    'variant_text' => $v,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // 4. SEED PRODUCTS, STOCK SNAPSHOTS & PRICE LISTS
        $products = [
            ['kode' => 'OBT-001', 'nama' => 'Amoxicillin 500mg Kapsul', 'satuan' => 'Box @ 10 Strip', 'status' => 'Tersedia', 'harga' => 45000],
            ['kode' => 'OBT-002', 'nama' => 'Paracetamol 500mg Kaplet', 'satuan' => 'Box @ 10 Strip', 'status' => 'Tersedia', 'harga' => 25000],
            ['kode' => 'OBT-003', 'nama' => 'Cefixime 100mg Kapsul', 'satuan' => 'Box @ 3 Strip', 'status' => 'Terbatas', 'harga' => 120000],
            ['kode' => 'OBT-004', 'nama' => 'Vitamin C 500mg Botol', 'satuan' => 'Botol @ 30 Tablet', 'status' => 'Habis', 'harga' => 18500],
            ['kode' => 'OBT-005', 'nama' => 'Antasida Doen Tablet Kunyah', 'satuan' => 'Box @ 10 Strip', 'status' => 'Tersedia', 'harga' => 15000],
        ];

        foreach ($products as $p) {
            $pId = DB::table('products')->insertGetId([
                'kode_produk' => $p['kode'],
                'nama_produk' => $p['nama'],
                'satuan' => $p['satuan'],
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('stock_snapshots')->insert([
                'product_id' => $pId,
                'status' => $p['status'],
                'source_updated_at' => $now,
                'notes' => 'Snapshot per ' . $now->format('d/m/Y H:i'),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('price_lists')->insert([
                'product_id' => $pId,
                'harga' => $p['harga'],
                'berlaku_dari' => $now->toDateString(),
                'berlaku_sampai' => $now->copy()->addMonths(3)->toDateString(),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 5. INITIAL SYNC LOG
        DB::table('sync_logs')->insert([
            'source' => 'navicat_seed',
            'status' => 'success',
            'total_records' => 5,
            'notes' => 'Inisialisasi awal basis data untuk demo sidang dan pengujian TA.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
