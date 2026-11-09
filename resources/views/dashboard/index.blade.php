<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Natashia Projek - Apotek WhatsApp CRM & AI Chatbot</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">

    <!-- Top Navigation Bar-->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center space-x-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-md shadow-emerald-500/20 font-bold text-xl">
                        Q
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <span class="font-extrabold text-lg text-slate-900 tracking-tight">Omnichannel CRM</span>
                            <span
                                class="bg-emerald-100 text-emerald-800 text-xs font-semibold px-2.5 py-0.5 rounded-full border border-emerald-200">Apotek
                                AI Bot</span>
                        </div>
                        <p class="text-xs text-slate-500">Tugas Akhir S-1 Informatika UKWK &bull; Natasia Utama
                            (31123024)</p>
                    </div>
                </div>

                <!-- Status System Badges -->
                <div class="hidden md:flex items-center space-x-3">
                    <div
                        class="flex items-center space-x-1.5 px-3 py-1 bg-slate-100 rounded-full border border-slate-200 text-xs font-medium text-slate-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Meta WhatsApp: <b>Siap Terhubung</b></span>
                    </div>
                    <div
                        class="flex items-center space-x-1.5 px-3 py-1 bg-slate-100 rounded-full border border-slate-200 text-xs font-medium text-slate-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>DB: <b>db_ta_natashia</b></span>
                    </div>
                    <div
                        class="flex items-center space-x-1.5 px-3 py-1 bg-slate-100 rounded-full border border-slate-200 text-xs font-medium text-slate-700">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span>Server: <b>Laragon Nginx</b></span>
                    </div>
                </div>

                <!-- User Profile -->
                <div class="flex items-center space-x-3">
                    <div
                        class="w-9 h-9 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-sm shadow">
                        BS
                    </div>
                    <div class="hidden sm:block text-right">
                        <p class="text-sm font-semibold text-slate-900">Budi Santoso</p>
                        <p class="text-xs text-slate-500">Sales Manager / Supervisor</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        <!-- KPI Summary Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Apotek Terdaftar</span>
                    <span class="p-2 rounded-xl bg-blue-50 text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3">
                    <span class="text-3xl font-bold text-slate-900">{{ $stats['total_customers'] }}</span>
                    <span class="text-xs text-slate-400 block mt-1">Pelanggan aktif apotek</span>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Pesan Masuk WA</span>
                    <span class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3">
                    <span class="text-3xl font-bold text-slate-900">{{ $stats['total_messages'] }}</span>
                    <span class="text-xs text-emerald-600 font-semibold block mt-1">&bull; Idempoten Meta API</span>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">FAQ Knowledge Base</span>
                    <span class="p-2 rounded-xl bg-purple-50 text-purple-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3">
                    <span class="text-3xl font-bold text-slate-900">{{ $stats['total_faqs'] }} FAQ</span>
                    <span class="text-xs text-purple-600 font-semibold block mt-1">19 variasi semantik</span>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Tiket Eskalasi</span>
                    <span class="p-2 rounded-xl bg-amber-50 text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="mt-3">
                    <span class="text-3xl font-bold text-slate-900">{{ $stats['open_tickets'] }}</span>
                    <span class="text-xs text-amber-600 font-semibold block mt-1">SLA 30 Menit Aktif</span>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div
                class="border-b border-slate-200 bg-slate-50/50 px-6 flex space-x-6 overflow-x-auto text-sm font-semibold">
                <button onclick="switchTab('simulator')" id="tab-btn-simulator"
                    class="py-4 border-b-2 border-emerald-600 text-emerald-600 flex items-center space-x-2 whitespace-nowrap">
                    <span>🤖 Simulator Chatbot WhatsApp</span>
                    <span class="bg-emerald-100 text-emerald-700 text-xs px-2 py-0.5 rounded-full">Uji Langsung</span>
                </button>
                <button onclick="switchTab('faq')" id="tab-btn-faq"
                    class="py-4 border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center space-x-2 whitespace-nowrap">
                    <span>📚 5 FAQ Resmi Apotek</span>
                </button>
                <button onclick="switchTab('products')" id="tab-btn-products"
                    class="py-4 border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center space-x-2 whitespace-nowrap">
                    <span>📦 Katalog Obat & Stok</span>
                </button>
                <button onclick="switchTab('tickets')" id="tab-btn-tickets"
                    class="py-4 border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center space-x-2 whitespace-nowrap">
                    <span>📋 Tiket Retur & Eskalasi SLA</span>
                </button>
                <button onclick="switchTab('customers')" id="tab-btn-customers"
                    class="py-4 border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center space-x-2 whitespace-nowrap">
                    <span>🏥 Daftar Apotek & Sales</span>
                </button>
                <a href="{{ route('order.review') }}" target="_blank"
                    class="py-2.5 my-2 px-3.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center space-x-1.5 whitespace-nowrap text-xs font-bold transition ml-auto">
                    <span>🛒 Buka Form Review Obat (Pasien)</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>

            <!-- TAB 1: SIMULATOR CHATBOT (Fitur Utama Pengujian Interaktif) -->
            <div id="tab-content-simulator" class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- WhatsApp Chat Mockup -->
                    <div
                        class="lg:col-span-7 bg-[#efeae2] rounded-2xl p-4 border border-slate-300 shadow-inner flex flex-col h-[520px]">
                        <!-- WA Header -->
                        <div
                            class="bg-[#075e54] text-white p-3 rounded-xl flex items-center justify-between shadow mb-3">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center font-bold text-sm">
                                    🏥
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold leading-tight">Asisten Resmi Apotek AI</h4>
                                    <p class="text-[11px] text-emerald-200">Online &bull; WhatsApp Cloud API</p>
                                </div>
                            </div>
                            <span class="text-xs bg-emerald-700/60 px-2 py-1 rounded text-white">Akun Bisnis
                                Resmi</span>
                        </div>

                        <!-- Chat Messages Container -->
                        <div id="chat-messages" class="flex-1 overflow-y-auto space-y-3 p-2 text-sm">
                            <div class="flex justify-start">
                                <div
                                    class="bg-white rounded-2xl rounded-tl-none p-3 shadow max-w-[85%] text-slate-800 border border-slate-100">
                                    <p class="font-semibold text-xs text-emerald-700 mb-1">Bot Apotek AI</p>
                                    <p>Halo! Selamat datang di Layanan Otomatis WhatsApp Apotek. Silakan tanyakan
                                        perihal estimasi pengiriman, bukti kunjungan sales, retur barang, atau stok
                                        obat.</p>
                                    <span class="text-[10px] text-slate-400 block text-right mt-1">Baru saja</span>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Input Box -->
                        <form id="chat-form" onsubmit="sendSimulatedMessage(event)" class="mt-3 flex space-x-2">
                            <input type="text" id="chat-input"
                                placeholder="Ketik pertanyaan pelanggan apotek di sini..." required
                                class="flex-1 px-4 py-2.5 rounded-full border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white text-sm">
                            <button type="submit" id="btn-send"
                                class="px-5 py-2.5 bg-[#128c7e] hover:bg-[#075e54] text-white rounded-full font-semibold text-sm transition shadow flex items-center space-x-1">
                                <span>Kirim</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </form>
                    </div>

                    <!-- Side Panel: Deteksi Semantik & Metrik AI -->
                    <div class="lg:col-span-5 space-y-4">
                        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-200">
                            <h4 class="font-bold text-sm text-slate-900 mb-3 flex items-center space-x-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span>Contoh Pertanyaan untuk Dicoba:</span>
                            </h4>
                            <div class="space-y-2">
                                <button onclick="fillAndSend('kapan obat pesanan saya sampai ya mas?')"
                                    class="w-full text-left text-xs bg-white hover:bg-emerald-50 hover:border-emerald-300 p-2.5 rounded-xl border border-slate-200 transition text-slate-700">
                                    &bull; <i>"kapan obat pesanan saya sampai ya mas?"</i> (FAQ 1: Pengiriman H+1)
                                </button>
                                <button onclick="fillAndSend('driver kirim barang jam berapa hari ini?')"
                                    class="w-full text-left text-xs bg-white hover:bg-emerald-50 hover:border-emerald-300 p-2.5 rounded-xl border border-slate-200 transition text-slate-700">
                                    &bull; <i>"driver kirim barang jam berapa hari ini?"</i> (FAQ 2: Rute Hari H)
                                </button>
                                <button
                                    onclick="fillAndSend('ada obat yang kemasannya rusak pecah pas sampe, mau retur')"
                                    class="w-full text-left text-xs bg-white hover:bg-emerald-50 hover:border-emerald-300 p-2.5 rounded-xl border border-slate-200 transition text-slate-700">
                                    &bull; <i>"ada obat yang kemasannya rusak pecah pas sampe, mau retur"</i> (FAQ 3:
                                    Retur & Foto)
                                </button>
                                <button onclick="fillAndSend('minta bukti sales yang kemarin mampir ke apotek')"
                                    class="w-full text-left text-xs bg-white hover:bg-emerald-50 hover:border-emerald-300 p-2.5 rounded-xl border border-slate-200 transition text-slate-700">
                                    &bull; <i>"minta bukti sales yang kemarin mampir ke apotek"</i> (FAQ 4: Bukti Visit)
                                </button>
                                <button onclick="fillAndSend('minta info daftar harga dan brosur obat')"
                                    class="w-full text-left text-xs bg-white hover:bg-emerald-50 hover:border-emerald-300 p-2.5 rounded-xl border border-slate-200 transition text-slate-700">
                                    &bull; <i>"minta info daftar harga dan brosur obat"</i> (FAQ 5: Info Produk &
                                    Brosur)
                                </button>
                            </div>
                        </div>

                        <!-- Live AI Match Insight Card -->
                        <div id="ai-insight" class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
                            <h4 class="font-bold text-sm text-slate-900 mb-2">📊 Analisis Semantik AI Real-Time</h4>
                            <p class="text-xs text-slate-500 mb-3">Hasil pencocokan kemiripan pertanyaan pelanggan ke
                                basis data FAQ:</p>

                            <div class="space-y-2 text-xs">
                                <div class="flex justify-between py-1.5 border-b border-slate-100">
                                    <span class="text-slate-500">FAQ Terdeteksi:</span>
                                    <span id="metric-faq" class="font-semibold text-slate-800">-</span>
                                </div>
                                <div class="flex justify-between py-1.5 border-b border-slate-100">
                                    <span class="text-slate-500">Skor Keyakinan (Confidence):</span>
                                    <span id="metric-score" class="font-bold text-emerald-600">-</span>
                                </div>
                                <div class="flex justify-between py-1.5 border-b border-slate-100">
                                    <span class="text-slate-500">Waktu Respon:</span>
                                    <span id="metric-time" class="font-semibold text-slate-800">-</span>
                                </div>
                                <div class="flex justify-between py-1.5">
                                    <span class="text-slate-500">Status Keputusan:</span>
                                    <span id="metric-status"
                                        class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-600">Menunggu
                                        Input</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: 5 FAQ RESMI APOTEK -->
            <div id="tab-content-faq" class="p-6 hidden space-y-4">
                <div class="flex justify-between items-center mb-2">
                    <div>
                        <h3 class="font-bold text-base text-slate-900">5 Canonical FAQ Resmi Proposal Tugas Akhir</h3>
                        <p class="text-xs text-slate-500">Sumber: PROJEK.docx & REV3 Laporan Proposal (Jawaban
                            terstandar & anti-halusinasi)</p>
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach ($faqs as $faq)
                        <div
                            class="bg-white border border-slate-200 rounded-2xl p-5 hover:border-emerald-300 transition shadow-sm">
                            <div class="flex items-start justify-between">
                                <div class="space-y-1">
                                    <span
                                        class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        FAQ #{{ $faq->id }} &bull; {{ $faq->kategori }}
                                    </span>
                                    <h4 class="font-bold text-slate-900 text-sm mt-1">{{ $faq->canonical_question }}
                                    </h4>
                                </div>
                                <span class="text-xs text-slate-400 font-medium">{{ $faq->variants->count() }} variasi
                                    kata</span>
                            </div>
                            <div
                                class="mt-3 p-3.5 bg-slate-50 rounded-xl border border-slate-100 text-xs text-slate-700 leading-relaxed">
                                <b class="text-slate-900">Jawaban Standar Bot:</b><br>
                                {{ $faq->answer }}
                            </div>
                            <div class="mt-3 flex flex-wrap gap-1.5">
                                @foreach ($faq->variants as $variant)
                                    <span
                                        class="bg-slate-100 text-slate-600 px-2.5 py-1 rounded-lg text-[11px] border border-slate-200">
                                        {{ $variant->variant_text }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- TAB 3: KATALOG OBAT & STOK SNAPSHOT -->
            <div id="tab-content-products" class="p-6 hidden">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="font-bold text-base text-slate-900">Katalog Produk Obat & Snapshot Stok</h3>
                        <p class="text-xs text-slate-500">Sesuai FR-04: Menampilkan stok berkala dengan disclaimer
                            update</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-600 uppercase font-semibold border-b border-slate-200">
                            <tr>
                                <th class="p-3">Kode Produk</th>
                                <th class="p-3">Nama Obat</th>
                                <th class="p-3">Satuan</th>
                                <th class="p-3">Harga (Price List)</th>
                                <th class="p-3">Status Stok</th>
                                <th class="p-3">Update Terakhir</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($products as $prod)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="p-3 font-mono font-bold text-slate-700">{{ $prod->kode_produk }}</td>
                                    <td class="p-3 font-semibold text-slate-900">{{ $prod->nama_produk }}</td>
                                    <td class="p-3 text-slate-600">{{ $prod->satuan }}</td>
                                    <td class="p-3 font-bold text-emerald-700">
                                        Rp {{ number_format($prod->latestPrice?->harga ?? 0, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3">
                                        @php $stok = $prod->latestStock?->status ?? 'Tersedia'; @endphp
                                        @if ($stok === 'Tersedia')
                                            <span
                                                class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">Tersedia</span>
                                        @elseif($stok === 'Terbatas')
                                            <span
                                                class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">Terbatas</span>
                                        @else
                                            <span
                                                class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800">Habis</span>
                                        @endif
                                    </td>
                                    <td class="p-3 text-slate-400">
                                        {{ $prod->latestStock?->source_updated_at ?? 'Hari ini' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 4: TIKET RETUR & ESKALASI SLA -->
            <div id="tab-content-tickets" class="p-6 hidden">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="font-bold text-base text-slate-900">Manajemen Tiket Retur & SLA 30 Menit</h3>
                        <p class="text-xs text-slate-500">Tiket otomatis terbuat bila ada retur fisik obat rusak atau
                            eskalasi ke Sales Manager</p>
                    </div>
                </div>

                @if ($tickets->isEmpty())
                    <div class="text-center py-12 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                        <p class="text-sm font-semibold text-slate-700">Belum ada tiket aktif saat ini</p>
                        <p class="text-xs text-slate-400 mt-1">Coba kirimkan simulasi pesan <i>"ada obat pecah mau
                                retur"</i> pada tab Simulator untuk membuat tiket baru.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead
                                class="bg-slate-50 text-slate-600 uppercase font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="p-3">No. Tiket</th>
                                    <th class="p-3">Tipe</th>
                                    <th class="p-3">Nama Apotek</th>
                                    <th class="p-3">Sales Pendamping</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3">SLA 30 Menit</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($tickets as $t)
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="p-3 font-mono font-bold text-emerald-700">{{ $t->ticket_code }}
                                        </td>
                                        <td class="p-3 uppercase font-semibold text-slate-700">{{ $t->type }}
                                        </td>
                                        <td class="p-3 font-medium text-slate-900">
                                            {{ $t->customer?->nama_apotek ?? '-' }}</td>
                                        <td class="p-3 text-slate-600">
                                            {{ $t->assignedSales?->name ?? 'Belum Ditugaskan' }}</td>
                                        <td class="p-3">
                                            <span
                                                class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 uppercase">{{ $t->status }}</span>
                                        </td>
                                        <td class="p-3 text-rose-600 font-semibold">{{ $t->sla_due_at ?? '30 menit' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- TAB 5: DAFTAR APOTEK -->
            <div id="tab-content-customers" class="p-6 hidden">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="font-bold text-base text-slate-900">Daftar Pelanggan Apotek Terverifikasi</h3>
                        <p class="text-xs text-slate-500">Nomor WhatsApp apotek yang diidentifikasi oleh bot saat pesan
                            masuk (FR-01)</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($customers as $c)
                        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm space-y-2">
                            <div class="flex justify-between items-start">
                                <h4 class="font-bold text-sm text-slate-900">{{ $c->nama_apotek }}</h4>
                                <span
                                    class="text-[10px] font-mono bg-slate-100 px-2 py-0.5 rounded text-slate-600 font-bold">{{ $c->kode_apotek }}</span>
                            </div>
                            <p class="text-xs text-slate-600 flex items-center space-x-1">
                                <span>📞</span>
                                <span class="font-mono font-semibold">{{ $c->wa_number }}</span>
                            </p>
                            <p class="text-xs text-slate-500">{{ $c->alamat }}</p>
                            <div class="pt-2 border-t border-slate-100 flex justify-between items-center text-[11px]">
                                <span class="text-slate-400">Sales Area:</span>
                                <span
                                    class="font-semibold text-emerald-700">{{ $c->defaultSales?->name ?? 'Semua Sales' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500">
        <p>&copy; 2026 Tugas Akhir S-1 Informatika Uwika. All rights reserved. &bull; <a href="#"
                class="text-emerald-600 hover:underline">Documentation</a></p>
    </footer>

    <!-- Interactive JavaScript -->
    <script>
        function switchTab(tab) {
            const tabs = ['simulator', 'faq', 'products', 'tickets', 'customers'];
            tabs.forEach(t => {
                const btn = document.getElementById('tab-btn-' + t);
                const content = document.getElementById('tab-content-' + t);
                if (t === tab) {
                    btn.classList.add('border-emerald-600', 'text-emerald-600');
                    btn.classList.remove('border-transparent', 'text-slate-500');
                    content.classList.remove('hidden');
                } else {
                    btn.classList.remove('border-emerald-600', 'text-emerald-600');
                    btn.classList.add('border-transparent', 'text-slate-500');
                    content.classList.add('hidden');
                }
            });
        }

        function fillAndSend(text) {
            document.getElementById('chat-input').value = text;
            document.getElementById('chat-form').dispatchEvent(new Event('submit'));
        }

        async function sendSimulatedMessage(e) {
            e.preventDefault();
            const input = document.getElementById('chat-input');
            const message = input.value.trim();
            if (!message) return;

            const chatMessages = document.getElementById('chat-messages');

            // 1. Tampilkan Bubble Pesan User
            const userBubble = document.createElement('div');
            userBubble.className = 'flex justify-end';
            userBubble.innerHTML = `
                <div class="bg-[#dcf8c6] rounded-2xl rounded-tr-none p-3 shadow max-w-[85%] text-slate-800 border border-emerald-100">
                    <p class="font-semibold text-xs text-slate-600 mb-0.5">Anda (Apotek Pelanggan)</p>
                    <p>${escapeHtml(message)}</p>
                    <span class="text-[10px] text-slate-400 block text-right mt-1">Baru saja &bull; Terkirim</span>
                </div>
            `;
            chatMessages.appendChild(userBubble);
            input.value = '';
            chatMessages.scrollTop = chatMessages.scrollHeight;

            // 2. Tampilkan Animasi Mengetik Bot
            const typingBubble = document.createElement('div');
            typingBubble.id = 'typing-bubble';
            typingBubble.className = 'flex justify-start';
            typingBubble.innerHTML = `
                <div class="bg-white rounded-2xl rounded-tl-none p-2.5 shadow text-xs text-slate-400 italic border border-slate-100">
                    Asisten bot sedang mencocokkan semantik FAQ...
                </div>
            `;
            chatMessages.appendChild(typingBubble);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            try {
                const response = await fetch('/api/simulator/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        message: message
                    })
                });

                const data = await response.json();
                typingBubble.remove();

                // 3. Tampilkan Respon Bot
                const botBubble = document.createElement('div');
                botBubble.className = 'flex justify-start';
                botBubble.innerHTML = `
                    <div class="bg-white rounded-2xl rounded-tl-none p-3 shadow max-w-[85%] text-slate-800 border border-slate-100">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-xs text-emerald-700">Bot Apotek AI</span>
                            ${data.matched ? '<span class="bg-emerald-50 text-emerald-700 text-[10px] px-1.5 py-0.5 rounded font-bold border border-emerald-200">FAQ Terstandar</span>' : '<span class="bg-amber-50 text-amber-700 text-[10px] px-1.5 py-0.5 rounded font-bold border border-amber-200">Eskalasi ke Sales</span>'}
                        </div>
                        <p class="leading-relaxed whitespace-pre-line">${escapeHtml(data.reply)}</p>
                        <span class="text-[10px] text-slate-400 block text-right mt-1">${data.response_time_ms} ms</span>
                    </div>
                `;
                chatMessages.appendChild(botBubble);
                chatMessages.scrollTop = chatMessages.scrollHeight;

                // 4. Update Panel Analisis Semantik AI
                document.getElementById('metric-faq').innerText = data.matched_faq ?
                    `#${data.matched_faq.id} (${data.matched_faq.kategori})` : 'Tidak Cocok (Diteruskan)';
                document.getElementById('metric-score').innerText = (data.confidence_score * 100).toFixed(1) + '%';
                document.getElementById('metric-time').innerText = data.response_time_ms + ' ms';

                const statusBadge = document.getElementById('metric-status');
                if (data.matched) {
                    statusBadge.className = 'px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800';
                    statusBadge.innerText = 'Dijawab Otomatis (Akurat)';
                } else {
                    statusBadge.className = 'px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-800';
                    statusBadge.innerText = 'Diteruskan ke Sales';
                }

            } catch (err) {
                if (typingBubble) typingBubble.remove();
                alert('Terjadi kesalahan saat menghubungi server simulator.');
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>

</html>
