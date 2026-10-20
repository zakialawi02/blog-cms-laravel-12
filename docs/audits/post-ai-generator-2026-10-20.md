# Audit Post AI Generator — 20 Oktober 2026

Lingkup: halaman `/dashboard/posts/ai-generator`, endpoint ide topik, `AiService`, job generator, konfigurasi queue, penyimpanan artikel, riwayat, dan pemulihan job. Audit menggunakan kode saat ini; tidak mengubah implementasi atau memanggil provider AI nyata.

P1 berarti perlu segera diperbaiki; P2 berarti masalah fungsional atau keamanan yang perlu dijadwalkan. Ditemukan **10 masalah: 3 P1 dan 7 P2**.

Status tindak lanjut: temuan 5 sudah diperbaiki pada 20 Oktober 2026. Sembilan temuan lainnya belum ditangani dalam perubahan ini.

## Temuan

### 1. [P1] HTML hasil AI dapat menjalankan JavaScript di dashboard

Lokasi: `resources/views/pages/dashboard/posts/partials/ai-generator-table.blade.php:27`; `app/Jobs/GenerateAiArticleJob.php:146`; `resources/views/pages/front/posts/singlePost.blade.php:35`.

`{!! $item->result !!}` memasukkan respons provider langsung ke DOM. Class `hidden` hanya menyembunyikan tampilannya, tidak menetralkan `<script>` atau event handler. Respons AI yang berisi kode berbahaya dapat berjalan ketika pemilik membuka riwayat, bahkan sebelum artikel disetujui. Isi artikel juga disimpan tanpa sanitasi dan dirender mentah di halaman artikel setelah dipublikasikan.

Pembuktian: respons palsu berisi `<script>window.auditProbe=1</script>` tetap utuh pada HTML riwayat dan konten artikel tersimpan. Audit memeriksa markup; tidak mengeksekusi payload di browser.

Perbaikan: escape respons pada tempat penyimpanan teks untuk fitur copy/view; sanitasi konten artikel dengan allowlist HTML, atribut, dan protokol URL sebelum dirender. Instruksi dalam prompt bukan mekanisme sanitasi.

### 2. [P1] Koneksi Cloudflare menonaktifkan verifikasi TLS

Lokasi: `app/Services/AiService.php:214`.

`Http::withoutVerifying()` membuat client menerima sertifikat server yang tidak valid. Penyerang yang dapat menyadap koneksi berpeluang membaca bearer token dan prompt, atau mengganti respons artikel.

Pembuktian: opsi HTTP pada request palsu menunjukkan `verify = false`.

Perbaikan: aktifkan verifikasi sertifikat dan perbaiki CA bundle pada lingkungan yang memerlukan konfigurasi tambahan.

### 3. [P1] Reservasi queue berakhir sebelum batas waktu job

Lokasi: `config/queue.php:42`; `app/Jobs/GenerateAiArticleJob.php:43`.

Default `retry_after` database adalah 150 detik, sementara job dan request provider dapat berjalan sampai 600 detik. Dengan beberapa worker, job yang masih berjalan dapat diambil kembali sesudah 150 detik. Akibatnya muncul panggilan provider bersamaan, pemborosan kuota, dan potensi artikel ganda. Redis/Beanstalkd juga memiliki default 90 detik.

Pemeriksaan lokal menemukan `QUEUE_CONNECTION=database` tanpa override `DB_QUEUE_RETRY_AFTER` dalam `.env`. Konfigurasi efektif server produksi tidak diperiksa.

Perbaikan: gunakan urutan batas waktu HTTP < timeout job < retry_after; sesuaikan pengelola worker. Contoh retry_after 900 detik untuk job 600 detik, dengan waktu request HTTP yang menyisakan waktu untuk pemrosesan dan penyimpanan.

### 4. [P2] Penyimpanan artikel dan penyelesaian generasi tidak atomik

Lokasi: `app/Jobs/GenerateAiArticleJob.php:73`, `:146`, `:158`.

`Article::create()` dan update `article_id/status` merupakan operasi terpisah. Jika update generasi gagal setelah insert artikel berhasil, percobaan berikutnya masih melihat `article_id = null` dan membuat artikel tambahan. Pemeriksaan awal juga tidak melindungi dua job yang masuk bersamaan.

Pembuktian: simulasi exception ketika status pertama kali diubah menjadi `completed`, lalu menjalankan ulang job, menghasilkan **dua artikel untuk satu generasi**. Pengulangan berurutan setelah sukses normal memang tidak membuat duplikat.

Perbaikan: simpan artikel dan hasil generasi dalam satu transaksi; di dalam transaksi kunci dan periksa ulang baris generasi. Tambahkan mekanisme claim/lock sebelum request provider untuk mencegah biaya panggilan bersamaan. Jangan menahan transaksi database selama request AI.

### 5. [P2] API key Gemini ikut masuk log saat request gagal

**Status: diperbaiki, 20 Oktober 2026.** Request teks dan gambar kini mengirim key melalui header `x-goog-api-key`, bukan query URL, sesuai [dokumentasi autentikasi Gemini](https://ai.google.dev/api). Log error/konten kosong hanya menyimpan status HTTP dan endpoint; respons mentah provider tidak diteruskan ke log atau exception. Delapan test regresi (30 assertion) lulus untuk autentikasi header, error yang memantulkan key, konten kosong, dan kegagalan koneksi pada kedua jalur. Seluruh request pada test menggunakan HTTP fake. Uraian di bawah merekam kondisi sebelum perbaikan.

Lokasi: `app/Services/AiService.php:86`, `:104`.

URL request mengandung parameter `key`, lalu URL lengkap diteruskan ke `Log::error`. Kegagalan provider membuat credential tersalin ke penyimpanan log dan berpotensi ke sistem agregasi log.

Pembuktian: request Gemini palsu dengan key placeholder dan respons 503 menghasilkan konteks log yang masih memuat key tersebut.

Perbaikan: gunakan header autentikasi yang sesuai dan hilangkan atau redact credential dari URL, exception, dan konteks log. Tinjau kebutuhan rotasi key jika pola ini sudah berjalan pada lingkungan nyata.

### 6. [P2] Parser Cloudflare menolak respons Llama yang valid

Lokasi: `app/Services/AiService.php:231`; `config/ai.php`.

Service selalu membaca `result.choices.0.message.content`, sementara model Llama yang tersedia di pilihan model menggunakan output `response` pada endpoint native `/ai/run`. Respons HTTP 200 yang valid dianggap kosong.

Pembuktian: respons palsu `{"success":true,"result":{"response":"Valid Llama generated text"}}` menghasilkan exception `Cloudflare AI returned an empty response.`

Perbaikan: normalisasi respons berdasarkan kontrak model/endpoint atau gunakan endpoint kompatibel OpenAI secara konsisten. Tambahkan test respons setiap keluarga model.

Rujukan: [dokumentasi resmi Llama 3.2 3B Cloudflare](https://developers.cloudflare.com/workers-ai/models/llama-3.2-3b-instruct/), bagian Output. Klaim ini spesifik untuk Llama; tidak mengasumsikan semua model Cloudflare memakai skema yang sama.

### 7. [P2] Error sementara provider tidak mengikuti retry queue

Lokasi: `app/Services/AiService.php:422`; `app/Jobs/GenerateAiArticleJob.php:91`.

`generateArticle()` menangkap exception provider dan mengembalikan `success=false`. Job menandai generasi gagal lalu kembali normal. Dengan demikian, `$tries = 3` dan `$backoff = 30` tidak berlaku untuk error provider yang sudah ditangkap, termasuk 503, rate limit, dan gangguan koneksi.

Pembuktian: respons Gemini palsu 503 membuat job berakhir tanpa exception dengan status generasi `failed`.

Perbaikan: bedakan error permanen dan sementara; lempar kembali error sementara atau release job dengan jeda yang sesuai. Pertahankan pesan publik berupa referensi error.

### 8. [P2] Output kosong atau tidak lengkap dapat dianggap berhasil

Lokasi: `app/Services/AiService.php:111`; `app/Jobs/GenerateAiArticleJob.php:101`, `:127`, `:146`.

Service hanya menolak nilai `null`, bukan string kosong. Job tidak mensyaratkan judul dan badan artikel yang valid; fallback menerima seluruh respons sebagai konten. Respons kosong, penolakan model, atau keluaran terpotong dapat disimpan sebagai artikel dan ditandai `completed`.

Pembuktian: respons `success=true, data=''` membuat artikel kosong dan status `completed`.

Perbaikan: validasi respons nonkosong, struktur judul/badan, panjang kolom database, dan status penyelesaian provider sebelum menyimpan. Laporkan output tidak valid sebagai kegagalan yang dapat ditindaklanjuti.

### 9. [P2] Generate Ideas mengacak provider tanpa mengecek kesiapan credential

Lokasi: `app/Services/AiService.php:35`, `:442`.

Daftar model dibentuk dari seluruh konfigurasi, lalu ide topik memilih salah satunya secara acak. Jika instalasi hanya mengaktifkan Gemini, request tetap dapat memilih Sumopod, Qwen, atau Cloudflare yang credential-nya belum tersedia. Tombol yang sama bisa berhasil dan gagal bergantian tanpa perubahan input. Pilihan model pengguna juga tidak dipakai pada endpoint ini.

Dasar: penelusuran kode; tidak menguji credential akun atau mengirim request nyata.

Perbaikan: pilih provider/model yang sudah dikonfigurasi dan sehat; gunakan default eksplisit atau pilihan pengguna, dengan fallback yang terukur.

### 10. [P2] Refresh riwayat mengabaikan halaman pagination aktif

Lokasi: `resources/views/pages/dashboard/posts/ai-generator.blade.php:120`, `:166`.

Pada URL `?page=2`, tombol Refresh selalu mengambil URL index tanpa parameter `page`. Isi tabel berubah ke halaman pertama, sementara URL browser dan indikator pagination masih menunjukkan halaman kedua. Link pagination juga tidak diperbarui bersama tabel.

Dasar: penelusuran alur URL AJAX dan respons controller.

Perbaikan: pertahankan parameter halaman saat refresh dan perbarui metadata/link pagination bersama baris tabel.

## Verifikasi dan batas audit

- Lima test bawaan generator/recovery dicoba menggunakan SQLite di memori. Semuanya terhenti pada setup migrasi newsletter: `cannot drop PRIMARY KEY column: id`; **0 assertion** dijalankan. Ini batas verifikasi suite, bukan bukti kelima skenario fitur gagal.
- Probe terisolasi memakai migrasi tabel yang diperlukan, database SQLite di memori, HTTP fake, dan credential placeholder. Tidak ada request provider AI nyata. Probe mengonfirmasi temuan 1, 2, 4, 5, 6, 7, 8 serta perilaku idempotensi normal.
- Temuan 3, 9, dan 10 berasal dari pemeriksaan kode/konfigurasi; tidak melakukan uji beberapa worker atau browser end-to-end.
- Route memakai `auth`, `verified`, dan role `superadmin,admin,writer`. Riwayat difilter berdasarkan user aktif. Artikel baru disimpan sebagai `pending`.
- Test yang ada berfokus pada job dan recovery; belum mencakup kontrak HTTP provider, rendering hasil berbahaya, kegagalan di antara dua operasi penyimpanan, atau controller generator.

## Catatan operasional tambahan

- `composer dev` menjalankan `queue:listen --tries=1` tanpa `--queue=generator`, sedangkan controller mengirim job ke queue `generator`. Jika hanya memakai perintah bawaan tersebut pada konfigurasi default, job generator tidak diproses. Sediakan worker khusus dan dokumentasikan cara menjalankannya.
- Input `model` pada controller hanya divalidasi sebagai string. Service menolak model di luar allowlist setelah baris generasi dibuat dan job diterima. Validasi allowlist serta panjang maksimum di awal agar request invalid mendapat error validasi.
- Kedua endpoint POST AI belum memakai throttle atau kuota per pengguna. Pertimbangkan pembatasan frekuensi dan jumlah job aktif untuk mengendalikan biaya dan beban worker.

Urutan perbaikan: tutup eksekusi HTML berbahaya dan aktifkan TLS, selaraskan queue serta penyimpanan atomik, lalu perbaiki kontrak provider, retry, validasi output, dan perilaku UI.
