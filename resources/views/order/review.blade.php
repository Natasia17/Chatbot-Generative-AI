<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ringkasan & Konfirmasi Pesanan Obat - Apotek</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .touch-target { min-width: 44px; min-height: 44px; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 pb-32 antialiased">

    <!-- Top Navigation Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-20 shadow-sm">
        <div class="max-w-lg mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="/" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <div>
                    <h1 class="text-base font-bold text-slate-900 leading-tight">Review Pesanan Obat</h1>
                    <p class="text-[11px] text-emerald-700 font-medium">Layanan WhatsApp Resmi Apotek</p>
                </div>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                Online
            </span>
        </div>
    </header>

    <!-- Main Mobile Content Container -->
    <main class="max-w-lg mx-auto px-4 pt-4 space-y-4">

        <!-- Banner Info Pengiriman H+1 -->
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 flex items-start space-x-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center shrink-0 font-bold text-sm">
                🚚
            </div>
            <div class="text-xs text-emerald-900 leading-relaxed">
                <p class="font-bold">Estimasi Pengiriman: H+1</p>
                <p class="text-emerald-700 mt-0.5">Pesanan yang dikonfirmasi hari ini akan diproses untuk rute pengiriman keesokan harinya.</p>
            </div>
        </div>

        <!-- 1. Data Pemesan & Alamat -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm space-y-3">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center space-x-1.5">
                <span>📍</span>
                <span>Data Pelanggan / Apotek</span>
            </h2>

            <div class="space-y-2.5 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Pemesan / Apotek <span class="text-rose-500">*</span></label>
                    <input type="text" id="cust-name" value="{{ $defaultCustomer?->nama_apotek ?? 'Apotek Sehat Sentosa' }}"
                        placeholder="Contoh: Apotek Sehat Sentosa / Bu Siti"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800 text-xs">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                        <input type="tel" id="cust-phone" value="{{ $defaultCustomer?->wa_number ?? '0895398387040' }}"
                            placeholder="0812xxxxxxx"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800 font-mono text-xs">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Catatan Resep / Dokter (Opsional)</label>
                        <input type="text" id="order-notes" placeholder="Contoh: Butuh faktur pajak"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Alamat Lengkap Pengiriman <span class="text-rose-500">*</span></label>
                    <textarea id="cust-address" rows="2"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800 text-xs">{{ $defaultCustomer?->alamat ?? 'Jl. Raya Darmo No. 45, Surabaya' }}</textarea>
                </div>
            </div>
        </div>

        <!-- 2. Pemilihan & Review Obat -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center space-x-1.5">
                    <span>💊</span>
                    <span>Pilih Obat & Kuantitas</span>
                </h2>
                <span class="text-[11px] text-slate-400">Harga resmi terverifikasi</span>
            </div>

            <div class="divide-y divide-slate-100 space-y-3 pt-1">
                @foreach($products as $prod)
                @php
                    $price = (float) ($prod->latestPrice?->harga ?? 0);
                    $stockStatus = $prod->latestStock?->status ?? 'Tersedia';
                    $isAvailable = ($stockStatus !== 'Habis');
                @endphp
                <div class="pt-3 first:pt-0 flex items-center justify-between" id="product-card-{{ $prod->id }}" data-product-id="{{ $prod->id }}" data-price="{{ $price }}">
                    <div class="pr-3 flex-1">
                        <div class="flex items-center space-x-2">
                            <h3 class="font-bold text-xs text-slate-900 leading-snug">{{ $prod->nama_produk }}</h3>
                            @if($stockStatus === 'Tersedia')
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Tersedia</span>
                            @elseif($stockStatus === 'Terbatas')
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Terbatas</span>
                            @else
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Habis</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $prod->satuan }}</p>
                        <p class="text-xs font-bold text-emerald-700 mt-1">Rp {{ number_format($price, 0, ',', '.') }}</p>
                    </div>

                    <!-- Quantity Stepper Controls (Min 44x44px touch targets for mobile) -->
                    @if($isAvailable)
                    <div class="flex items-center space-x-2 shrink-0 bg-slate-100 rounded-xl p-1 border border-slate-200">
                        <button type="button" onclick="updateQty({{ $prod->id }}, -1)"
                            class="touch-target w-9 h-9 rounded-lg bg-white shadow-sm flex items-center justify-center font-bold text-slate-700 hover:bg-slate-200 active:scale-95 transition">
                            -
                        </button>
                        <span id="qty-val-{{ $prod->id }}" class="w-8 text-center font-bold text-xs text-slate-800">
                            {{ $loop->first ? '1' : '0' }}
                        </span>
                        <button type="button" onclick="updateQty({{ $prod->id }}, 1)"
                            class="touch-target w-9 h-9 rounded-lg bg-emerald-600 text-white shadow-sm flex items-center justify-center font-bold hover:bg-emerald-700 active:scale-95 transition">
                            +
                        </button>
                    </div>
                    @else
                    <div class="text-[11px] text-rose-500 font-semibold px-2 py-1 bg-rose-50 rounded-lg shrink-0">
                        Tidak Tersedia
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <!-- 3. Kartu Ringkasan Biaya (Price Breakdown) -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm space-y-3">
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center space-x-1.5">
                <span>🧾</span>
                <span>Rincian Estimasi Biaya</span>
            </h2>

            <div id="selected-summary-list" class="space-y-2 text-xs divide-y divide-slate-100">
                <!-- Diisi otomatis oleh JavaScript -->
            </div>

            <div class="pt-3 border-t border-slate-200 space-y-1.5 text-xs">
                <div class="flex justify-between text-slate-500">
                    <span>Subtotal Produk:</span>
                    <span id="label-subtotal" class="font-semibold text-slate-800">Rp 0</span>
                </div>
                <div class="flex justify-between text-slate-500">
                    <span>Biaya Kirim (Rute Sales):</span>
                    <span class="font-semibold text-emerald-700">Gratis (Sesuai Rute H+1)</span>
                </div>
                <div class="flex justify-between text-sm font-extrabold text-slate-900 pt-2 border-t border-dashed border-slate-200">
                    <span>Total Estimasi Biaya:</span>
                    <span id="label-total" class="text-emerald-700 font-extrabold text-base">Rp 0</span>
                </div>
            </div>

            <p class="text-[11px] text-slate-400 italic bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                * Ketersediaan fisik obat dan faktur resmi akan diverifikasi langsung oleh tim sales apotek saat menerima pesan konfirmasi WhatsApp Anda.
            </p>
        </div>

    </main>

    <!-- Sticky Bottom Ergonomic Action Bar for Mobile -->
    <div class="fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-slate-200 p-3 z-30 shadow-lg">
        <div class="max-w-lg mx-auto flex items-center justify-between space-x-3">
            <div>
                <span class="text-[10px] text-slate-400 block font-medium">Total Pesanan:</span>
                <span id="sticky-total" class="text-base font-extrabold text-slate-900 leading-tight">Rp 0</span>
            </div>

            <button type="button" id="btn-submit-wa" onclick="processOrderToWhatsApp()"
                class="touch-target flex-1 px-5 py-3 rounded-2xl bg-[#25D366] hover:bg-[#1ebe5b] active:scale-95 text-white font-bold text-xs flex items-center justify-center space-x-2 shadow-md shadow-emerald-500/20 transition">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.299.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086.159.058 1.011.477 1.184.564.173.087.289.13.332.203.043.072.043.419-.101.824z"/></svg>
                <span>Kirim Pesanan ke WhatsApp</span>
            </button>
        </div>
    </div>

    <!-- JavaScript Logika & Kalkulasi Interaktif -->
    <script>
        const state = {
            items: {}
        };

        // Inisialisasi kuantitas default saat halaman dimuat
        document.addEventListener('DOMContentLoaded', () => {
            @foreach($products as $prod)
                @if($loop->first && ($prod->latestStock?->status ?? 'Tersedia') !== 'Habis')
                    state.items[{{ $prod->id }}] = 1;
                @else
                    state.items[{{ $prod->id }}] = 0;
                @endif
            @endforeach
            calculateAndUpdateUI();
        });

        function updateQty(productId, delta) {
            const current = state.items[productId] || 0;
            const updated = Math.max(0, current + delta);
            state.items[productId] = updated;

            const spanEl = document.getElementById('qty-val-' + productId);
            if (spanEl) {
                spanEl.innerText = updated;
            }

            calculateAndUpdateUI();
        }

        async function calculateAndUpdateUI() {
            const rawItems = [];
            for (const [pId, qty] of Object.entries(state.items)) {
                if (qty > 0) {
                    rawItems.push({ product_id: parseInt(pId), qty: qty });
                }
            }

            if (rawItems.length === 0) {
                document.getElementById('selected-summary-list').innerHTML = `
                    <p class="text-slate-400 italic text-center py-2">Belum ada obat yang dipilih. Gunakan tombol + di atas untuk menambahkan kuantitas.</p>
                `;
                document.getElementById('label-subtotal').innerText = 'Rp 0';
                document.getElementById('label-total').innerText = 'Rp 0';
                document.getElementById('sticky-total').innerText = 'Rp 0';
                return;
            }

            try {
                const response = await fetch('/api/order/calculate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ items: rawItems })
                });

                const data = await response.json();
                if (data.success) {
                    let listHtml = '';
                    data.items.forEach(it => {
                        listHtml += `
                            <div class="flex justify-between items-center py-1.5">
                                <div>
                                    <span class="font-semibold text-slate-800">${escapeHtml(it.nama_produk)}</span>
                                    <span class="text-slate-400 block text-[11px]">${it.qty} x ${it.formatted_price}</span>
                                </div>
                                <span class="font-bold text-slate-900">${it.formatted_subtotal}</span>
                            </div>
                        `;
                    });

                    document.getElementById('selected-summary-list').innerHTML = listHtml;
                    document.getElementById('label-subtotal').innerText = data.formatted_total;
                    document.getElementById('label-total').innerText = data.formatted_total;
                    document.getElementById('sticky-total').innerText = data.formatted_total;
                }
            } catch (err) {
                console.error('Gagal kalkulasi:', err);
            }
        }

        async function processOrderToWhatsApp() {
            const name = document.getElementById('cust-name').value.trim();
            const phone = document.getElementById('cust-phone').value.trim();
            const address = document.getElementById('cust-address').value.trim();
            const notes = document.getElementById('order-notes').value.trim();

            if (!name || !phone || !address) {
                alert('Mohon lengkapi Nama, Nomor WhatsApp, dan Alamat pengiriman terlebih dahulu.');
                return;
            }

            const rawItems = [];
            for (const [pId, qty] of Object.entries(state.items)) {
                if (qty > 0) {
                    rawItems.push({ product_id: parseInt(pId), qty: qty });
                }
            }

            if (rawItems.length === 0) {
                alert('Pilih minimal 1 item obat dengan menekan tombol (+) sebelum mengirim pesanan.');
                return;
            }

            const btn = document.getElementById('btn-submit-wa');
            btn.disabled = true;
            btn.innerHTML = `<span>Membuat Draf WhatsApp...</span>`;

            try {
                const response = await fetch('/api/order/submit-wa', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        customer_name: name,
                        customer_phone: phone,
                        delivery_address: address,
                        notes: notes,
                        items: rawItems
                    })
                });

                const result = await response.json();
                if (result.success && result.whatsapp_url) {
                    // Buka link WhatsApp resmi
                    window.open(result.whatsapp_url, '_blank');
                } else {
                    alert('Gagal membuat pesanan: ' + (result.message || 'Terjadi kesalahan sistem.'));
                }
            } catch (err) {
                alert('Terjadi kesalahan koneksi server.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = `
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.007c.106.005.249-.04.39.299.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086.159.058 1.011.477 1.184.564.173.087.289.13.332.203.043.072.043.419-.101.824z"/></svg>
                    <span>Kirim Pesanan ke WhatsApp</span>
                `;
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
