<?php

namespace Tests\Unit;

use App\Models\Faq;
use App\Models\FaqVariant;
use App\Services\Ai\SemanticFaqMatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SemanticFaqMatcherTest extends TestCase
{
    use RefreshDatabase;

    protected SemanticFaqMatcher $matcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->matcher = new SemanticFaqMatcher();
    }

    public function test_normalizes_slang_and_punctuation(): void
    {
        $raw = "Halo mas, apotek gkmau ttd ato difoto sales ya!!";
        $normalized = $this->matcher->normalize($raw);

        $this->assertStringContainsString('tidak mau', $normalized);
        $this->assertStringContainsString('tanda tangan', $normalized);
        $this->assertStringContainsString('atau', $normalized);
        $this->assertStringNotContainsString('!', $normalized);
    }

    public function test_matches_faq_1_pengiriman_h_plus_1(): void
    {
        $query = "kapan pesanan obat saya dikirim ya mas?";
        $result = $this->matcher->match($query);

        $this->assertEquals('matched', $result['status']);
        $this->assertEquals('Pengiriman', $result['faq']->kategori);
        $this->assertStringContainsString('H+1', $result['answer']);
    }

    public function test_matches_faq_2_rute_pengiriman_hari_h(): void
    {
        $query = "jam berapa driver kurir sampai ke apotek saya?";
        $result = $this->matcher->match($query);

        $this->assertEquals('matched', $result['status']);
        $this->assertStringContainsString('rute alamat customer', $result['answer']);
    }

    public function test_matches_faq_3_kemasan_rusak_dan_retur(): void
    {
        $query = "ada barang yang bocor dan kemasan rusak saat diterima";
        $result = $this->matcher->match($query);

        $this->assertEquals('matched', $result['status']);
        $this->assertStringContainsString('Ajukan Retur', $result['answer']);
    }

    public function test_matches_faq_4_bukti_visit_sales(): void
    {
        $query = "kenapa sales visit minta ttd atau foto apoteker?";
        $result = $this->matcher->match($query);

        $this->assertEquals('matched', $result['status']);
        $this->assertStringContainsString('bukti kunjungan', $result['answer']);
    }

    public function test_matches_faq_5_product_knowledge(): void
    {
        $query = "bisa minta jadwal presentasi product knowledge obat baru?";
        $result = $this->matcher->match($query);

        $this->assertEquals('matched', $result['status']);
        $this->assertStringContainsString('5–6 bulan', $result['answer']);
    }

    public function test_returns_unmatched_for_out_of_scope_query(): void
    {
        $query = "berapa harga saham bca hari ini?";
        $result = $this->matcher->match($query);

        $this->assertEquals('unmatched', $result['status']);
        $this->assertNull($result['faq']);
    }
}
