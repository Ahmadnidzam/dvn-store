# DVNStore — Konteks Penyusunan Laporan Skripsi

## Tentang Proyek
DVNStore adalah platform distribusi digital berbasis web untuk developer indie lokal
Indonesia. Fokus produk: **aplikasi & game** (tidak ada kategori film). Stack: Laravel 12,
Bootstrap 5.3, MySQL, Midtrans Snap (pembayaran), Midtrans IRIS (withdraw — sementara
disabled karena menunggu aktivasi), VirusTotal API (scan file).

Path project lokal user: `C:\Users\NIZAM\dvn_store` (folder sudah pernah di-mount).

## Status Laporan
- Berikutnya: **Bab 1 → Bab 2 → Bab 3 → Bab 4**. Kerjakan **bertahap, satu bab per sesi**, minta
  approval dulu sebelum mulai.

## Format Laporan
- Mengikuti template skripsi **D3 Sistem Informasi Telkom University** (referensi
  "Campfy" sudah ada di knowledge proyek di file `referensi_proyek.docx`).
- Output: **Word .docx**, Times New Roman 12pt, line spacing 1.5, A4, margin 4-3-3-3 cm.
- Sitasi: IEEE `[1], [2], ...`. Daftar pustaka di akhir tiap bab.
- Struktur:
  - **Bab 1 — Persyaratan Sistem**: Latar belakang, rumusan masalah, tujuan.
  - **Bab 2 — Persyaratan Sistem**: Deskripsi Instansi/Pengguna, Gambaran Sistem Saat Ini,
    Analisis Proses Bisnis, Analisis Pengalaman Pengguna (User Persona, User Journey Map),
    Identifikasi Aplikasi Sejenis, Analisis Kebutuhan Sistem (fungsional + non-fungsional),
    Kinerja Sistem.
  - **Bab 3 — Pemodelan & Perancangan**: Arsitektur Sistem (komponen utama),
    Pemodelan Sistem dan Data (UML: Use Case, Activity, Class, Sequence; ERD),
    Perancangan Antarmuka (mockup tampilan Web), Kebutuhan Perangkat Keras/Lunak.
  - **Bab 4 — Implementasi & Pengujian**: Implementasi (screenshot tampilan hasil),
    Pengujian (Black-box test plan + hasil).

## Arsitektur Code yang Wajib Direfleksikan di Laporan


### Role-based
Tiga role di tabel `penggunas`: `user` (customer), `developer` (indie dev),
`admin` (via seeder dari `.env`, tidak via form). Form registrasi user dan developer
TERPISAH. Middleware `role:...` di semua route. Admin bisa block user/developer dan
takedown platform.

### Skema Database (`dvn_store_v2`)
- `penggunas` — id, name, email, password, role enum(user/developer/admin),
  status enum(active/blocked), blocked_at, blocked_reason, kode_unik, avatar
- `developer_profiles` — pengguna_id, nama_studio, deskripsi, website,
  bank_name, bank_account_number, bank_account_holder
- `platforms` — dev_id, category enum(app/game), nama_platform, slug, genre, harga,
  rating, icon, deskripsi, cuplikan, file_path, file_size, scan_status
  enum(pending/scanning/clean/infected/error), scan_result(JSON), is_published,
  is_taken_down, taken_down_at, taken_down_reason, upload_fee_transaksi_id,
  pending_file_path, pending_file_size, pending_scan_status enum(pending/scanning/clean/infected/error),
  pending_scan_result(JSON), pending_uploaded_at, file_updated_at (kolom fitur update file installer)
- `transaksis` — tipe enum(purchase/upload_fee), amount, platform_fee, net_amount,
  midtrans_order_id, snap_token, status enum(pending/paid/failed/expired/cancelled),
  paid_at, midtrans_response(JSON)
- `wallets` — dev_id, saldo (integer Rupiah)
- `wallet_transactions` — wallet_id, transaksi_id, tipe enum(credit/debit),
  amount, saldo_after, description
- `withdraws` — dev_id, amount, bank_snapshot(JSON), iris_payout_reference_no,
  status enum(pending/processing/success/failed/rejected), processed_at,
  iris_response(JSON)
- `reviews` — platform_id, user_id, rating(1-5), komentar, helpful_count
  (unique platform_id+user_id)
- `review_helpfuls` — review_id, user_id (unique)
- `downloads` — user_id, platform_id, transaksi_id (unique user_id+platform_id)
- `forum_posts` — user_id, content, helpful_count, is_hidden
- `forum_post_helpfuls` — post_id, user_id (unique)

### Struktur Direktori
```
dapat juga dilihat pada struktur_file_prompt.md
app/
├── Http/Controllers/{Auth, User/, Developer/, Admin/, Payment/}, ForumController
├── Http/Middleware/EnsureRole.php
├── Models/ (12 model)
├── Services/MidtransService, MidtransPayoutService, VirusTotalService
└── Jobs/ ScanUploadedFileJob (async, 2-fase upload→poll, scan produk baru)
         ScanPendingUpdateJob (async, 2-fase, scan file update; zero-downtime swap)
config/dvnstore.php (upload_fee, platform_fee_percent, min_withdraw, midtrans, iris, vt, admin)
database/migrations/2026_01_01_000001..11 (11 file schema baru)
database/seeders/AdminSeeder (baca .env)
resources/views/{Auth, User, Developer, Admin, Forum, Layouts, Components, Error}/
public/asset/css/ (design system: layout.css = root tokens, +6 file CSS per modul)
routes/web.php (75 route, group per role dengan middleware role:...)
```

## Aturan Bisnis
- **Upload fee Rp 10.000** per produk (`DVN_UPLOAD_FEE`, dibayar lewat Midtrans Snap).
- **Platform fee 10%** dari setiap penjualan (`DVN_PLATFORM_FEE_PERCENT`). Sisa 90%
  masuk wallet developer.
- **Minimum withdraw Rp 50.000** (`DVN_MIN_WITHDRAW`). Withdraw via Midtrans IRIS
  Disbursement (saat ini `IRIS_ENABLED=false` — sedang menunggu aktivasi).
- Semua nilai uang disimpan sebagai **integer Rupiah**, bukan float.
- File upload **WAJIB di-scan VirusTotal**. `is_published=false` sampai
  `scan_status='clean'`. File `infected` dihapus otomatis, fee tidak refund.
- **Update File gratis**: Developer dapat mengganti file installer tanpa bayar ulang.
  File baru di-scan VirusTotal via `ScanPendingUpdateJob` (2-fase: upload→polling).
  Selama scan, file lama tetap aktif (zero-downtime). Jika clean → swap otomatis +
  set `file_updated_at` (user di library dapat badge "Update tersedia"). Jika infected
  → file pengganti ditolak, file lama tetap aktif.

## Working Rules (Wajib)
- **Selalu Bahasa Indonesia** kecuali user pakai bahasa lain.
- **MINTA IZIN sebelum mengubah file**. Tunjukkan checklist file + diff garis besar.
- Sebelum tugas multi-step: pakai `AskUserQuestion` untuk klarifikasi.
- Tiap tugas non-trivial: buat TodoList dengan langkah verifikasi di akhir.
- Setelah selesai: ringkas perubahan, jangan ulang isi file.
- **JANGAN ubah UI** — user sudah redesign sendiri pakai skill UI/UX di Claude Code CLI.
  Kalau perlu visual untuk laporan, ambil **screenshot dari aplikasi yang jalan**
  (user yang ambil dan upload), jangan generate mockup baru.

## File yang TIDAK Boleh Diubah Tanpa Izin Eksplisit
- `bootstrap/app.php` (kecuali tambah middleware alias)
- `composer.lock`, `package-lock.json`
- File di `vendor/`, `storage/framework/`, `bootstrap/cache/`
- `database/migrations/0001_01_01_*` (default Laravel)
- `public/asset/css/*` dan semua file `resources/views/**.blade.php` (UI sudah final)

## Style Penulisan Laporan
- Pakai istilah teknis Inggris untuk programming (controller, route, migration, endpoint).
- Pakai istilah Indonesia untuk domain (pengguna, transaksi, pembayaran, pencairan).
- Formal akademik tapi mengalir, hindari kalimat berantai panjang.
- Tiap klaim faktual butuh sitasi IEEE.
- Diagram UML dan ERD: deskripsi proses dulu, baru gambar. Untuk implementasi
  diagram, user yang generate via tool (PlantUML/draw.io/Lucidchart) dan upload.
