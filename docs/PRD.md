# PRD — Chatbot WhatsApp untuk Customer Apotek, Sales, dan Sales Manager

| | |
|---|---|
| Versi | 0.1 (draft untuk review) |
| Jenis | Tugas Akhir, rencana implementasi |
| Platform | WhatsApp (integrasi langsung, **tanpa web app**) |
| Pengguna | Customer (apotek), Sales, Sales Manager |

> Legenda: **[ASUMSI]** = saya isi karena dokumen sumber belum menyebut; harus dikonfirmasi. **[TERBUKA]** = pertanyaan yang harus dijawab sebelum coding bagian terkait.

---

## 1. Latar belakang dan masalah

Customer (apotek) saat ini bertanya stok, harga, pengiriman, dan mengajukan retur lewat chat pribadi ke sales. Akibatnya jawaban tidak konsisten, bergantung ketersediaan sales, dan bukti (foto retur, kunjungan) tersebar di chat pribadi. Sales manager tidak punya visibilitas.

**Solusi:** satu nomor WhatsApp resmi perusahaan yang menjawab pertanyaan rutin (FAQ) secara konsisten, menyediakan info stok/harga terbaru, menerima foto retur dan PO, lalu mengeskalasi ke sales atau sales manager bila perlu.

## 2. Tujuan dan indikator keberhasilan

| Tujuan | Metrik (untuk bab pengujian TA) | Target [ASUMSI] |
|---|---|---|
| Mengurangi pertanyaan rutin ke sales | Persentase pertanyaan FAQ terjawab bot tanpa eskalasi | ≥ 70% |
| Jawaban akurat dan tidak mengarang | Akurasi pencocokan FAQ pada set uji (precision) | ≥ 85% |
| Respons cepat | Waktu balas bot (p95) | < 3 detik |
| Retur terdokumentasi | Persentase retur yang punya foto + data lengkap | 100% |
| Mudah dipakai | Skor SUS dari responden UAT | ≥ 68 |

## 3. Persona

| Peran | Kebutuhan utama |
|---|---|
| **Customer (apotek)** | Jawaban cepat soal pengiriman, stok, harga; cara mudah mengirim foto retur dan PO |
| **Sales** | Menerima eskalasi dan foto retur terstruktur; tahu customer mana yang butuh tindak lanjut |
| **Sales Manager** | Menerima eskalasi yang tidak ditangani sales; melihat foto retur; memantau ringkasan |

## 4. Ruang lingkup

### 4.1 Dalam lingkup (dari dokumen "Tools")
1. Chatbot **terbatas** menjawab sesuai FAQ yang terdaftar.
2. Eskalasi ke sales dan sales manager.
3. Customer cek update stok dan price list.
4. Customer kirim foto untuk keperluan retur produk.
5. Sales dan sales manager melihat foto yang dikirim customer.
6. Integrasi WhatsApp.
7. Menerima PO dari customer (sebatas *menerima*).

### 4.2 Batasan yang disengaja (dari dokumen)
| Batasan | Interpretasi dan dampak desain |
|---|---|
| Real stok | Bot **tidak** terhubung real-time ke ERP. Stok berasal dari data yang diperbarui berkala. Bot menampilkan **status** (Tersedia / Terbatas / Habis) beserta **"per tanggal/jam update"**, bukan angka pasti. [ASUMSI] |
| Price list update | Harga dari price list yang diunggah/diperbarui admin, selalu disertai tanggal berlaku dan disclaimer "dapat berubah". [ASUMSI] |
| Hanya sampai menerima PO | Bot menerima dan meneruskan PO ke sales. **Tidak** ada pembayaran, invoice, atau konfirmasi order otomatis. |
| Menerima foto untuk retur | Bot hanya mengumpulkan bukti dan data retur. **Tidak** memutuskan retur disetujui atau ditolak. |

### 4.3 Di luar lingkup
Web/dashboard admin, transaksi dan pembayaran via chat, integrasi ERP real-time, aplikasi mobile native, jawaban bebas di luar FAQ.

## 5. Keputusan arsitektur utama

### 5.1 Tanpa web: bagaimana data dikelola?
Pertanyaan yang muncul di chat ("yakin ga perlu dashboard buat setting?") sah, dan penguji hampir pasti menanyakannya: *siapa yang mengubah FAQ, stok, harga, dan nomor sales?* Rekomendasi agar tetap **tanpa web**:

| Opsi | Cara kerja | Kelebihan | Kekurangan |
|---|---|---|---|
| **A. Google Sheets sebagai panel admin (direkomendasikan)** | FAQ, stok, price list, mapping customer-sales disimpan di Sheet; backend sinkron berkala atau via perintah | Nol UI yang harus dibangun, familiar bagi staf, ada riwayat revisi | Perlu service account dan penanganan validasi |
| B. Upload file (CSV/XLSX) lewat WhatsApp oleh manager | Manager kirim file + perintah `/update stok` | Murni WA | Rawan salah format, validasi lebih rumit |
| C. Seed manual lewat database/artisan | Admin teknis yang mengubah | Paling cepat dibuat | Tidak realistis untuk operasional |

Perintah singkat khusus Sales Manager lewat WA (`/sync`, `/status`) dipakai untuk memicu sinkronisasi dan melihat ringkasan. [TERBUKA: konfirmasi ke pihak apotek/pembimbing bahwa Opsi A diterima]

### 5.2 Stack yang disarankan [ASUMSI: folder berada di `C:\laragon\www`]
- **Backend:** Laravel 11 (PHP 8.3) di Laragon, MySQL, queue driver `database` (webhook harus cepat membalas 200, proses berat masuk antrean).
- **WhatsApp:** Meta **WhatsApp Business Cloud API** (resmi). Library tidak resmi (mis. Baileys) hanya sebagai cadangan demo dan harus dicatat sebagai risiko di laporan (rawan blokir, tidak sesuai ToS).
- **Pencocokan FAQ:** hybrid. Normalisasi teks (huruf kecil, hapus tanda baca, kamus slang: "gamau", "ato", "ttd"), pencarian kemiripan (TF-IDF/BM25 atau trigram), lalu ambang kepercayaan. Di bawah ambang: tanya klarifikasi satu kali, lalu eskalasi. LLM opsional di belakang feature flag dan **hanya boleh memilih entri FAQ**, tidak menulis jawaban bebas, supaya sesuai batasan "terbatas sesuai FAQ".
- **Penyimpanan media:** foto diunduh dari Graph API segera setelah diterima (URL media Meta kedaluwarsa), disimpan di storage lokal/privat dengan nama acak.

### 5.3 Catatan teknis WhatsApp yang sering menjebak
1. **Jendela 24 jam:** pesan yang diprakarsai bisnis di luar 24 jam sejak pesan terakhir user wajib memakai **template message** yang disetujui Meta. Notifikasi eskalasi ke sales/manager bisa jatuh di luar jendela, jadi siapkan template (mis. `eskalasi_baru`, `retur_baru`) dan jadikan sales/manager "memulai chat" ke nomor bot agar jendela terbuka.
2. **Webhook:** verifikasi tanda tangan `X-Hub-Signature-256`, tangani pesan duplikat (idempoten berdasarkan `message.id`), balas 200 cepat.
3. **Mode pengembangan:** nomor uji Meta hanya bisa mengirim ke daftar penerima terbatas. Cukup untuk demo sidang, tetapi harus disebut di batasan.
4. **Tombol interaktif:** reply button maksimal 3, list message maksimal 10 baris. Desain menu mengikuti batas ini.

## 6. Kebutuhan fungsional

### FR-01 Identifikasi peran
- Peran ditentukan dari nomor WA terdaftar (`users` untuk sales/manager, `customers` untuk apotek).
- Nomor tidak dikenal: bot meminta **kode pelanggan** atau nama apotek; jika tidak cocok, eskalasi ke sales wilayah/manager. Tidak ada data harga/stok untuk nomor yang belum terverifikasi. [ASUMSI]
- *Acceptance:* Given nomor tidak terdaftar, When mengetik "cek harga", Then bot meminta verifikasi dan tidak menampilkan harga.

### FR-02 Menu utama (customer)
Menu list: **Tanya FAQ · Cek Stok & Harga · Ajukan Retur · Kirim PO · Hubungi Sales**. Teks bebas tetap diproses lewat FR-03.

### FR-03 Jawab FAQ
- Sumber jawaban hanya tabel `faqs` (lihat §8 untuk 5 FAQ awal).
- Skor ≥ ambang: kirim jawaban + tanya "Sudah terjawab?" (Ya/Tidak).
- Skor menengah: tawarkan 2–3 FAQ terdekat sebagai pilihan.
- Skor rendah atau user menjawab "Tidak": buat eskalasi (FR-07).
- Semua pertanyaan, skor, dan hasilnya dicatat untuk evaluasi.
- *Acceptance:* Given pertanyaan "kapan barang dikirim?", Then bot menjawab FAQ pengiriman H+1. Given pertanyaan di luar FAQ ("harga saham?"), Then bot tidak mengarang dan menawarkan eskalasi.

### FR-04 Cek stok dan price list
- Customer mengetik nama produk (pencarian fuzzy) atau memilih dari hasil.
- Jawaban: nama produk, **status stok**, harga sesuai price list yang berlaku, **timestamp update terakhir**, dan disclaimer.
- Produk tidak ditemukan: tawarkan saran terdekat atau eskalasi.
- Data basi (lebih dari N jam/hari, konfigurable) memicu peringatan tambahan "data mungkin belum diperbarui". [ASUMSI]

### FR-05 Pengajuan retur dengan foto
Alur percakapan terstruktur:
1. Bot menanyakan: nama produk, nomor batch (opsional), jumlah, alasan (rusak/kedaluwarsa/salah kirim/lainnya), nomor faktur/PO (jika ada).
2. Bot meminta 1–5 foto (batas ukuran dan tipe MIME divalidasi: jpeg/png/webp).
3. Bot mengunduh dan menyimpan foto, membuat **tiket retur** `RTR-YYYYMMDD-XXXX`, memberi nomor tiket ke customer.
4. Bot memberi tahu sales (dan manager bila tidak ditindak dalam batas waktu) dengan ringkasan tiket + **foto diteruskan langsung di WA**.
- Bot **tidak** menjanjikan persetujuan retur. Kalimat penutup: "Tim sales akan meninjau dan menghubungi Anda."
- *Acceptance:* Given tiket retur selesai, Then sales mendapat pesan berisi data tiket dan semua foto; tiket tersimpan dengan status `baru`.

### FR-06 Terima PO
- Customer mengirim PO sebagai teks, foto, atau dokumen (PDF).
- Bot menyimpan, membuat nomor referensi, meneruskan ke sales, dan menjawab: "PO diterima, bukan konfirmasi order. Sales akan memproses." Tidak ada perhitungan harga, invoice, atau pembayaran.

### FR-07 Eskalasi
- Pemicu: jawaban FAQ tidak memuaskan, permintaan "hubungi sales", kata kunci komplain, retur/PO baru, atau bot gagal dua kali.
- Rute: **sales yang dipetakan ke customer** (`customer_sales`) → bila tidak ada respons dalam SLA (default 30 menit jam kerja [ASUMSI], bisa diubah) → **sales manager**.
- Mode *handoff*: pesan customer diteruskan ke sales dengan ID tiket; balasan sales (dengan reply pada pesan notifikasi atau format `#ID pesan`) diteruskan ke customer. Bot berhenti menjawab otomatis pada percakapan itu sampai tiket ditutup (`/tutup #ID`).
- Di luar jam kerja: bot memberi tahu jam layanan dan menyimpan tiket. [TERBUKA: jam kerja]

### FR-08 Akses sales dan manager
- Perintah ringkas lewat WA: `/tiket` (daftar tiket terbuka milikku), `/retur #ID` (lihat detail + foto), `/tutup #ID`.
- Manager tambahan: `/ringkasan` (jumlah eskalasi, retur, rata-rata waktu respons hari ini/minggu ini), `/sync` (sinkron ulang data dari Sheet).
- Hak akses diperiksa per peran; sales hanya melihat tiket customer miliknya, manager melihat semua.

### FR-09 Pencatatan dan audit
Semua pesan masuk/keluar, keputusan bot (intent, skor), perubahan status tiket, dan akses media dicatat dengan timestamp.

## 7. Kebutuhan non-fungsional
| Area | Kebutuhan |
|---|---|
| Keamanan | Verifikasi signature webhook; token di `.env`, tidak masuk repo; storage foto privat; pembatasan akses per peran |
| Privasi | Data kontak dan foto minimal yang diperlukan; kebijakan retensi (mis. foto retur dihapus setelah N bulan) [ASUMSI]; selaras prinsip UU PDP |
| Keandalan | Idempoten pada webhook; antrean dengan retry dan dead-letter; pesan gagal terkirim tercatat |
| Kinerja | Webhook balas < 1 detik; respons bot p95 < 3 detik |
| Ketahanan | Bila sinkron Sheet gagal, bot memakai data terakhir yang valid + peringatan usia data |
| Observabilitas | Log terstruktur, ID korelasi per percakapan |
| Lokalisasi | Bahasa Indonesia santai-sopan; mengerti singkatan umum apotek/sales |

## 8. Basis pengetahuan FAQ awal (dari dokumen)

| # | Topik | Isi jawaban (dirapikan dari catatan sumber) |
|---|---|---|
| 1 | Waktu pengiriman | Pengiriman dilakukan H+1 setelah order dibuat atau diterima sales. |
| 2 | Rute pengiriman | Pengiriman hari H mengikuti rute alamat customer, mohon bersabar menunggu. |
| 3 | Kemasan dan kendala | Kemasan yang diterima sesuai dan dalam kondisi baik. Jika ada kendala, langsung lanjut ke pengiriman foto sebagai bukti agar dapat dicek ulang sebelum kemungkinan retur. (**memicu alur FR-05**) |
| 4 | Bukti kunjungan sales | Sales yang visit ke apotek wajib memberikan bukti kunjungan (tanda tangan atau foto) untuk melengkapi bukti visit. |
| 5 | Product knowledge | Permintaan product knowledge ke apotek minimal 1 bulan sekali, dengan rentang 5–6 bulan sebelum kembali ke apotek yang sama. |
| 6 | **[TERBUKA]** | Di dokumen sumber, poin 6 masih kosong. Isi atau hapus. |

> Catatan kualitas: tiap FAQ perlu **3–5 variasi pertanyaan** (termasuk bahasa santai) supaya pencocokan akurat. Ini juga yang dipakai untuk set uji akurasi.

## 9. Model data (ringkas)

```
customers(id, kode, nama_apotek, wa_number, alamat, sales_id, verified_at)
users(id, nama, wa_number, role[sales|manager], aktif)
customer_sales(customer_id, sales_id)             -- bila 1 customer banyak sales
conversations(id, customer_id, state, handoff_ticket_id, last_inbound_at)
messages(id, wa_message_id UNIQUE, conversation_id, direction, type, body, media_id, created_at)
faqs(id, kategori, pertanyaan_kanonik, jawaban, aktif)
faq_variants(id, faq_id, teks)
products(id, kode, nama, satuan)
stock_snapshots(id, product_id, status, updated_at_source)
price_lists(id, product_id, harga, berlaku_dari, berlaku_sampai)
tickets(id, kode, tipe[eskalasi|retur|po], customer_id, assigned_to, status, sla_due_at, created_at, closed_at)
return_requests(ticket_id, produk, batch, qty, alasan, no_faktur)
media_files(id, ticket_id, wa_media_id, path, mime, size, sha256)
bot_events(id, conversation_id, intent, score, outcome, created_at)  -- bahan evaluasi TA
```

## 10. Alur utama

```
Customer ──WA──> Meta Cloud API ──webhook──> Laravel
   verifikasi signature → simpan pesan (idempoten) → queue
   queue: identifikasi peran → state machine percakapan
      ├ FAQ      → matcher → jawab / klarifikasi / eskalasi
      ├ Stok/Harga → pencarian produk → status + timestamp
      ├ Retur    → kumpulkan data → unduh foto → tiket → notifikasi sales
      ├ PO       → simpan → tiket → notifikasi sales
      └ Eskalasi → sales → (SLA lewat) → manager
Sales/Manager ──WA──> perintah (/tiket, /retur, /tutup) atau balasan handoff
```

## 11. Strategi pengujian dan evaluasi (untuk Bab Pengujian)
1. **Unit test:** normalisasi teks, matcher FAQ, state machine, validasi media, perhitungan SLA.
2. **Integration test:** simulasi payload webhook Meta (teks, tombol, gambar, dokumen, pesan duplikat, signature salah).
3. **Uji akurasi FAQ:** minimal 100 pertanyaan berlabel (termasuk 20–30 di luar FAQ) → precision, recall, tingkat salah-jawab. Laporkan juga ambang yang dipilih dan alasannya.
4. **Uji alur end-to-end:** retur dengan foto, PO, eskalasi dan naik ke manager saat SLA lewat.
5. **UAT:** responden dari customer, sales, manager → SUS + wawancara singkat.
6. **Uji negatif:** nomor tak dikenal, file bukan gambar, spam, pesan sangat panjang.

## 12. Risiko
| Risiko | Mitigasi |
|---|---|
| Aturan Meta (template, jendela 24 jam) menghambat notifikasi | Siapkan template sejak awal; sales/manager membuka jendela dengan menyapa bot |
| Data stok/harga basi | Tampilkan timestamp; peringatan data lama |
| Bot salah menjawab | Ambang kepercayaan + konfirmasi "sudah terjawab?" + eskalasi |
| Foto berisi data sensitif | Akses terbatas, retensi, audit akses |
| Ketergantungan satu nomor WA/akun Meta | Dokumentasikan setup; fallback demo dengan nomor uji |
| Scope melebar ke transaksi | Batasan §4.2 dijaga dan ditulis eksplisit di laporan |

## 13. Rencana pengerjaan
| Fase | Isi | Keluaran |
|---|---|---|
| 0 | Baca dokumentasi, setup tooling, konfirmasi pertanyaan terbuka | Catatan setup, daftar keputusan |
| 1 | Skema DB, webhook + verifikasi + simpan pesan | Webhook berjalan di nomor uji |
| 2 | Matcher FAQ + menu + identifikasi peran | FR-01–03 lulus uji |
| 3 | Stok/harga + sinkron Sheet | FR-04 |
| 4 | Retur + foto + PO | FR-05, FR-06 |
| 5 | Eskalasi, SLA, perintah sales/manager | FR-07, FR-08 |
| 6 | Pengujian, UAT, perbaikan, dokumentasi sidang | Laporan pengujian |

## 14. Perspektif Dosen Penguji: pertanyaan yang sebaiknya sudah siap jawabannya
1. Apa bedanya ini dengan sekadar menu WhatsApp otomatis? Apa kontribusi/nilai tambahnya? *(jawab: pencocokan teks bebas + eskalasi terstruktur + bukti retur terdokumentasi + metrik terukur)*
2. Mengapa bot dibatasi hanya FAQ? *(kontrol risiko jawaban keliru di konteks farmasi/distribusi)*
3. Bagaimana Anda menjamin bot tidak mengarang? Berapa ambang kepercayaan dan bagaimana menentukannya?
4. Siapa yang mengubah FAQ, stok, harga? Mengapa tidak ada dashboard? *(§5.1)*
5. Dari mana data stok berasal dan seberapa mutakhir? Apa dampaknya jika salah?
6. Bagaimana keamanan foto dan data customer? Apa dasar retensinya?
7. Mengapa memilih Cloud API resmi dibanding library tidak resmi? Apa keterbatasannya?
8. Bagaimana pengujian dilakukan dan bagaimana metrik dihitung? Berapa ukuran sampel?
9. Apa yang terjadi saat webhook dikirim dua kali atau server mati sementara?
10. Apa keterbatasan sistem dan rencana pengembangan lanjutan? *(integrasi ERP, transaksi, dashboard analitik)*

## 15. Pertanyaan terbuka (blokir)
1. Isi FAQ poin 6?
2. Sumber dan frekuensi update stok serta price list (file apa, siapa yang mengirim)?
3. Jumlah sales, wilayah, dan aturan pemetaan customer–sales?
4. Jam kerja dan SLA eskalasi yang disepakati?
5. Apakah Opsi A (Google Sheets) disetujui sebagai pengganti dashboard?
6. Format PO yang umum diterima (foto, PDF, teks)?
7. Nomor WhatsApp yang dipakai produksi atau cukup nomor uji untuk sidang?
8. Tanggal sidang, untuk menentukan batas fase.
