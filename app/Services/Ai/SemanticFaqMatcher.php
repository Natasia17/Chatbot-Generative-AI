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
     * Cocokkan query pelanggan ke basis data FAQ
     *
     * @return array{status: string, confidence: float, faq: Faq|null, answer: string|null, suggestions: Collection}
     */
    public function match(string $query, float $threshold = 0.50): array
    {
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
