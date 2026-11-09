<?php

namespace App\Services\Ai;

use App\Models\Faq;
use App\Models\FaqVariant;
use Illuminate\Support\Collection;

class SemanticFaqMatcher
{
    protected array $slangDictionary = [
        'gamau' => 'tidak mau',
        'gkmau' => 'tidak mau',
        'gak mau' => 'tidak mau',
        'gk mau' => 'tidak mau',
        'enggak' => 'tidak',
        'nggak' => 'tidak',
        'ato' => 'atau',
        'ataw' => 'atau',
        'ttd' => 'tanda tangan',
        'ttangan' => 'tanda tangan',
        'tanda tgn' => 'tanda tangan',
        'pk' => 'product knowledge',
        'pengetahuan produk' => 'product knowledge',
        'brg' => 'barang',
        'brng' => 'barang',
        'kpn' => 'kapan',
        'kapanan' => 'kapan',
        'sdh' => 'sudah',
        'udh' => 'sudah',
        'udah' => 'sudah',
        'blm' => 'belum',
        'blom' => 'belum',
        'smpe' => 'sampai',
        'sampe' => 'sampai',
        'tibanya' => 'sampai',
        'bcor' => 'bocor',
        'rusk' => 'rusak',
        'kemasn' => 'kemasan',
        'pech' => 'pecah',
        'penyok' => 'rusak',
        'drvr' => 'driver kurir',
        'sopir' => 'driver kurir',
        'ongkir' => 'pengiriman',
        'delivery' => 'pengiriman',
        'kirimnya' => 'pengiriman',
        'dikirim' => 'pengiriman',
        'orderan' => 'pesanan',
        'po' => 'pesanan',
    ];

    protected array $stopwords = [
        'yang', 'dan', 'di', 'ke', 'dari', 'ini', 'itu', 'untuk', 'pada', 
        'adalah', 'atau', 'saat', 'jika', 'bisa', 'ya', 'mas', 'mbak', 
        'mohon', 'tolong', 'ada', 'apa', 'apakah'
    ];

    /**
     * Normalisasi teks input apotek
     */
    public function normalize(string $text): string
    {
        $clean = mb_strtolower(trim($text));
        // Hapus tanda baca
        $clean = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $clean);
        // Hapus spasi ganda
        $clean = preg_replace('/\s+/', ' ', $clean);

        // Substitusi kamus slang
        $words = explode(' ', $clean);
        $normalizedWords = [];
        foreach ($words as $w) {
            $normalizedWords[] = $this->slangDictionary[$w] ?? $w;
        }

        return trim(implode(' ', $normalizedWords));
    }

    /**
     * Hitung kemiripan teks menggunakan kombinasi Query Coverage, Jaccard overlap, dan Levenshtein
     */
    public function calculateSimilarity(string $query, string $target): float
    {
        $qNorm = $this->normalize($query);
        $tNorm = $this->normalize($target);

        if ($qNorm === $tNorm) {
            return 1.0;
        }

        $qTokens = array_values(array_filter(explode(' ', $qNorm)));
        $tTokens = array_values(array_filter(explode(' ', $tNorm)));

        if (empty($qTokens) || empty($tTokens)) {
            return 0.0;
        }

        // Filter kata-kata henti (stopwords) untuk ekstraksi kata kunci bermakna
        $qClean = array_values(array_diff($qTokens, $this->stopwords));
        $tClean = array_values(array_diff($tTokens, $this->stopwords));

        if (empty($qClean)) {
            $qClean = $qTokens;
        }
        if (empty($tClean)) {
            $tClean = $tTokens;
        }

        $inter = array_intersect($qClean, $tClean);
        $union = array_unique(array_merge($qClean, $tClean));

        // 1. Coverage / Kepenuhan kata kunci pertanyaan di target
        $coverage = count($inter) / count($qClean);

        // 2. Jaccard overlap
        $jaccard = count($union) > 0 ? (count($inter) / count($union)) : 0.0;

        // 3. Levenshtein string similarity
        $maxLen = max(strlen($qNorm), strlen($tNorm));
        $levDist = levenshtein($qNorm, $tNorm);
        $levSim = $maxLen > 0 ? (1.0 - ($levDist / $maxLen)) : 0.0;
        if ($levSim < 0) {
            $levSim = 0.0;
        }

        // Pembobotan: 60% Keyword Coverage + 25% Jaccard + 15% Levenshtein
        $score = ($coverage * 0.60) + ($jaccard * 0.25) + ($levSim * 0.15);

        return round($score, 4);
    }

    /**
     * Deteksi apakah query merupakan sapaan awal atau permintaan menu utama (FR-02)
     */
    public function isGreeting(string $query): bool
    {
        $clean = trim($this->normalize($query));
        $tokens = array_values(array_filter(explode(' ', $clean)));

        $greetings = [
            'hai', 'halo', 'hello', 'hi', 'p', 'pagi', 'siang', 'sore', 'malam',
            'selamat pagi', 'selamat siang', 'selamat sore', 'selamat malam',
            'assalamualaikum', 'assalamu alaikum', 'salam', 'permisi',
            'menu', 'help', 'bantuan', 'mulai', 'start', 'tes', 'test', 'ping', 'info'
        ];

        if (in_array($clean, $greetings, true)) {
            return true;
        }

        if (count($tokens) <= 2) {
            foreach ($tokens as $token) {
                if (in_array($token, ['hai', 'halo', 'hi', 'pagi', 'siang', 'malam', 'menu', 'tes', 'test', 'p'], true)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Deteksi apakah query merupakan ucapan terima kasih
     */
    public function isGratitude(string $query): bool
    {
        $clean = trim($this->normalize($query));
        $gratitudes = [
            'terima kasih', 'terimakasih', 'makasih', 'tengkyu', 'thank you', 'thanks', 'tq', 'matur nuwun'
        ];

        foreach ($gratitudes as $g) {
            if (str_contains($clean, $g)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Cocokkan query pelanggan ke basis data FAQ
     *
     * @return array{status: string, confidence: float, faq: mixed, answer: string|null, suggestions: Collection}
     */
    public function match(string $query, float $threshold = 0.50): array
    {
        // 1. Tangani Sapaan & Menu Layanan Utama (FR-02)
        if ($this->isGreeting($query)) {
            $greetingAnswer = "Halo! Selamat datang di Layanan Otomatis WhatsApp Apotek Mitra Resmi.\n\nSaya asisten virtual apotek yang siap membantu Anda. Silakan tanyakan hal seputar:\n\n1️⃣ *Estimasi Pengiriman:* Waktu kirim pesanan obat (H+1)\n2️⃣ *Rute Driver:* Jadwal & rute kirim kurir hari ini\n3️⃣ *Retur Barang:* Syarat & prosedur kemasan obat rusak/bocor\n4️⃣ *Kunjungan Sales:* Konfirmasi & bukti visit sales apotek\n5️⃣ *Katalog & Harga:* Info stok obat dan daftar harga resmi\n\nAtau Anda dapat langsung mengetikkan pertanyaan Anda, contoh: *\"Kapan pesanan saya dikirim?\"*";

            return [
                'status' => 'matched',
                'confidence' => 1.0,
                'faq' => (object) [
                    'id' => null,
                    'kategori' => 'Sapaan & Menu Layanan (FR-02)',
                    'canonical_question' => 'Sapaan Awal & Menu Layanan Apotek',
                    'answer' => $greetingAnswer,
                ],
                'answer' => $greetingAnswer,
                'suggestions' => collect(),
            ];
        }

        // 2. Tangani Ucapan Terima Kasih
        if ($this->isGratitude($query)) {
            $gratitudeAnswer = "Sama-sama! Senang dapat membantu Anda. Jika ada kebutuhan obat lainnya atau ada hal yang perlu ditanyakan lagi, jangan ragu untuk menghubungi kami kembali ya. Semoga lekas sembuh dan sehat selalu!";

            return [
                'status' => 'matched',
                'confidence' => 1.0,
                'faq' => (object) [
                    'id' => null,
                    'kategori' => 'Penutup & Ucapan Terima Kasih',
                    'canonical_question' => 'Ucapan Terima Kasih Pelanggan',
                    'answer' => $gratitudeAnswer,
                ],
                'answer' => $gratitudeAnswer,
                'suggestions' => collect(),
            ];
        }

        $faqs = Faq::with('variants')->where('is_active', true)->get();

        $scored = $faqs->map(function ($faq) use ($query) {
            $bestScore = $this->calculateSimilarity($query, $faq->canonical_question);

            foreach ($faq->variants as $variant) {
                $vScore = $this->calculateSimilarity($query, $variant->variant_text);
                if ($vScore > $bestScore) {
                    $bestScore = $vScore;
                }
            }

            return [
                'faq' => $faq,
                'score' => $bestScore,
            ];
        })->sortByDesc('score');

        $topMatch = $scored->first();

        if ($topMatch && $topMatch['score'] >= $threshold) {
            return [
                'status' => 'matched',
                'confidence' => $topMatch['score'],
                'faq' => $topMatch['faq'],
                'answer' => $topMatch['faq']->answer,
                'suggestions' => collect(),
            ];
        }

        if ($topMatch && $topMatch['score'] >= 0.35) {
            $suggestions = $scored->take(2)->pluck('faq');
            return [
                'status' => 'clarification',
                'confidence' => $topMatch['score'],
                'faq' => $topMatch['faq'],
                'answer' => null,
                'suggestions' => $suggestions,
            ];
        }

        return [
            'status' => 'unmatched',
            'confidence' => $topMatch ? $topMatch['score'] : 0.0,
            'faq' => null,
            'answer' => null,
            'suggestions' => collect(),
        ];
    }
}
