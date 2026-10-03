# reCAPTCHA pada Register — Setup & Operasional

Dokumen ini menjelaskan cara mengaktifkan, mematikan, dan memverifikasi proteksi
Google reCAPTCHA pada form register web (`POST /register`) dan opsional pada API
(`POST /api/auth/register`).

## 1. Ringkasan

| Item | Keterangan |
|---|---|
| Versi terpasang | **reCAPTCHA v3** (invisible / berbasis skor) — key yang dipasang bertipe v3. v2 checkbox juga didukung lewat `RECAPTCHA_VERSION=v2` |
| Rule validasi | `app/Rules/RecaptchaRule.php` (dipakai web & API) |
| Config | `config/services.php` → `services.recaptcha` |
| Verifikasi | server-side ke `https://www.google.com/recaptcha/api/siteverify` |
| Aktif bila | `RECAPTCHA_ENABLED=true` **dan** `RECAPTCHA_SECRET_KEY` terisi |

**Penting:** selama `RECAPTCHA_SECRET_KEY` masih kosong, verifikasi **tidak
dijalankan** dan register berjalan seperti biasa. Jadi kode ini aman dideploy
sebelum kredensial diisi (tidak ada risiko pengguna tidak bisa mendaftar).

### Catatan v3 (mode yang dipakai sekarang)

- **Tidak ada widget/checkbox** — pengguna tidak melihat apa pun. Token
  dibuat otomatis oleh JavaScript tepat sebelum form dikirim.
- Google mengembalikan **skor 0.0–1.0** (1.0 = sangat mungkin manusia).
  Verifikasi ditolak bila skor < `RECAPTCHA_MIN_SCORE` (default `0.5`).
- **Token sekali pakai** dan berlaku ±2 menit. Karena itu token diambil saat
  submit, bukan saat halaman dimuat.
- Setiap skor yang diterima dicatat di log (`reCAPTCHA v3 score accepted`,
  level *debug*) dan yang ditolak di level *info* — pakai data ini untuk
  menyetel `RECAPTCHA_MIN_SCORE`:

  ```bash
  grep "reCAPTCHA v3 score" storage/logs/laravel.log | tail -n 20
  ```

  Kalau banyak pengguna sah ditolak, turunkan ambang (mis. `0.3`).
- Key v3 **tidak** bisa dirender sebagai checkbox v2 — kalau suatu saat ingin
  checkbox, buat key baru bertipe v2 di konsol reCAPTCHA lalu set
  `RECAPTCHA_VERSION=v2`.


## 2. Mengaktifkan (langkah manual)

1. Buka https://www.google.com/recaptcha/admin → **Create** (atau buka site yang ada).
2. Pilih tipe key: **reCAPTCHA v3** (score based, invisible) — ini yang dipakai
   sekarang. Kalau ingin checkbox, pilih **reCAPTCHA v2 → "I'm not a robot"
   Checkbox** dan set `RECAPTCHA_VERSION=v2`.
3. Domains: `zakialawi.com`, `www.zakialawi.com` (tambahkan domain lain bila perlu).
4. Salin **Site key** dan **Secret key**.
5. Isi ke `.env` server (`/www/wwwroot/zakialawi.com/.env`):

   ```env
   RECAPTCHA_VERSION=v2
   RECAPTCHA_SITE_KEY=<site-key>
   RECAPTCHA_SECRET_KEY=<secret-key>
   RECAPTCHA_ENABLED=true
   ```

6. Bersihkan cache config:

   ```bash
   php artisan config:clear
   ```

7. Verifikasi: buka `https://zakialawi.com/register` → widget "I'm not a robot" muncul.

## 3. Mematikan (kill switch)

```env
RECAPTCHA_ENABLED=false
```

lalu `php artisan config:clear`. Atau kosongkan `RECAPTCHA_SECRET_KEY=` —
keduanya membuat verifikasi berhenti tanpa perlu ubah kode.

## 4. Variabel lengkap

| Variabel | Default | Fungsi |
|---|---|---|
| `RECAPTCHA_VERSION` | `v2` | `v2` (checkbox) atau `v3` (invisible, berbasis skor) |
| `RECAPTCHA_SITE_KEY` | – | Kunci publik, dipakai widget di halaman |
| `RECAPTCHA_SECRET_KEY` | – | Kunci rahasia, **wajib** agar verifikasi aktif |
| `RECAPTCHA_ENABLED` | `true` | Kill switch global |
| `RECAPTCHA_ENABLED_FOR_API` | `false` | Aktifkan verifikasi di `POST /api/auth/register` |
| `RECAPTCHA_MIN_SCORE` | `0.5` | Hanya v3 — skor minimum yang diterima |
| `RECAPTCHA_FAIL_OPEN` | `false` | `false` = tolak kalau Google tidak bisa dihubungi (fail-closed) |
| `RECAPTCHA_TIMEOUT` | `5` | Timeout request ke Google (detik) |

## 5. Perilaku saat gagal

| Kondisi | Hasil |
|---|---|
| Token kosong saat aktif | Ditolak — "Please tick \"I'm not a robot\" to continue." |
| Token kedaluwarsa (`timeout-or-duplicate`) | Ditolak — "Verification expired. Please tick the box again." |
| Google menolak token | Ditolak — "Security verification failed. Please try again." |
| Google tidak bisa dihubungi / timeout | Ditolak (fail-closed) — "Security verification is unavailable right now..." |
| Google error + `RECAPTCHA_FAIL_OPEN=true` | Diterima (fail-open) + dicatat di log |
| Skor v3 < `RECAPTCHA_MIN_SCORE` | Ditolak |

Semua kegagalan verifikasi dicatat di `storage/logs/laravel.log`
(`reCAPTCHA verification failed` / `reCAPTCHA verification error`).

## 6. API register (opsional)

Default `RECAPTCHA_ENABLED_FOR_API=false` → perilaku API **tidak berubah**.

Kalau diaktifkan, client mengirim field tambahan pada body JSON:

```json
{
  "name": "Test",
  "username": "testapi",
  "email": "test@example.com",
  "password": "Password123!",
  "password_confirmation": "Password123!",
  "recaptcha_token": "<token dari widget>"
}
```

Gagal verifikasi → `422` dengan `errors.recaptcha_token`.

API register juga dibatasi **5 percobaan per IP per 5 menit** → `429`
`Too many registration attempts. Please try again later.` (berlaku terlepas
dari reCAPTCHA).

## 7. Catatan teknis

- **Mengapa rule bersifat implicit:** Laravel melewatkan rule non-implicit saat
  nilai field kosong. Karena kita perlu menolak token kosong, `RecaptchaRule`
  menyetel properti `$implicit = true` (dibaca `InvokableValidationRule::make()`).
  Tanpa ini, token kosong akan dianggap lolos.
- **Kenapa tidak pakai package pihak ketiga:** cukup satu rule + satu config;
  tidak ada dependency baru yang harus di-audit.
- **SRI:** `https://www.google.com/recaptcha/api.js` tidak mendukung
  Subresource Integrity (isinya di-generate dinamis per site key). Mitigasi:
  script hanya dimuat di halaman `/register`, bukan di layout global.
- **Widget hanya dirender** kalau `site_key` **dan** `secret_key` sama-sama
  terisi — mencegah widget muncul sementara backend tidak memverifikasi.

## 8. Test

```bash
php artisan test --filter=Recaptcha   # 29 test
php artisan test                      # full suite
```

Test memakai `Http::fake()` sehingga tidak melakukan panggilan nyata ke Google.
