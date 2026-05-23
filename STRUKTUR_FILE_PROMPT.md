# DVNStore — Struktur File Project

Berikut pemetaan direktori lengkap project Laravel DVNStore. Tiap file diberi
komentar singkat (apa isinya / untuk apa) supaya context cepat dipahami.

```
dvn_store/
│
├── .env                          # Konfigurasi runtime: DB, Midtrans keys, IRIS, VirusTotal,
│                                 # admin seeder, dan platform rules (upload fee, dst).
├── composer.json                 # Dependency PHP: laravel/framework, midtrans/midtrans-php, guzzlehttp/guzzle
├── package.json                  # Dependency JS: vite, axios
├── artisan                       # CLI Laravel
├── referensi_proyek.docx   # referensi susunan laporan
├── LAPORAN_CONTEXT_PROMPT.md     # Konteks penyusunan laporan (untuk chat baru)
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php                          # Login, logout, register user (form), register developer (form), forget password
│   │   │   ├── Controller.php                              # Base controller Laravel
│   │   │   ├── ForumController.php                         # Forum global: index, store (post), toggleHelpful (vote)
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php                 # Statistik global: total user/dev/platform/GMV/revenue
│   │   │   │   ├── UserController.php                      # List user, block, unblock (status enum di penggunas)
│   │   │   │   ├── DeveloperController.php                 # List developer, detail, block, unblock
│   │   │   │   ├── PlatformController.php                  # List semua platform, takedown, restore
│   │   │   │   ├── TransactionController.php               # Riwayat transaksi & withdraw global
│   │   │   │   └── ForumController.php                     # Moderasi forum: hide, unhide, destroy
│   │   │   ├── Developer/
│   │   │   │   ├── DashboardController.php                 # Analytics dev: penjualan/bulan, top produk, total revenue
│   │   │   │   ├── PlatformController.php                  # CRUD produk dev + alur bayar upload fee Midtrans
│   │   │   │   ├── WalletController.php                    # Lihat saldo + history mutasi wallet_transactions
│   │   │   │   └── WithdrawController.php                  # Request withdraw IRIS (saat ini disabled via flag)
│   │   │   ├── User/
│   │   │   │   ├── HomeController.php                      # Dashboard, search, detail platform, profile, edit profile
│   │   │   │   ├── PurchaseController.php                  # Free download + buy (generate Midtrans Snap token)
│   │   │   │   ├── ReviewController.php                    # Tulis review + toggle helpful vote
│   │   │   │   └── LibraryController.php                   # Daftar unduhan + download file installer
│   │   │   └── Payment/
│   │   │       ├── MidtransCallbackController.php          # Webhook Midtrans Snap: update status transaksi → fulfill purchase/upload_fee
│   │   │       └── MidtransPayoutCallbackController.php    # Webhook Midtrans IRIS: update status withdraw + refund kalau gagal
│   │   └── Middleware/
│   │       └── EnsureRole.php                              # Cek session login + role + fetch fresh status (anti stale block)
│   ├── Jobs/
│   │   ├── ScanUploadedFileJob.php                         # Async 2-fase: upload installer BARU ke VirusTotal → polling, update is_published
│   │   └── ScanPendingUpdateJob.php                        # Async 2-fase: scan file UPDATE (pending_*); kalau clean → swap ke file_path zero-downtime
│   ├── Models/                                             # 12 model
│   │   ├── Pengguna.php                                    # User table; helper isAdmin/isDeveloper/isUser/isBlocked + relasi
│   │   ├── DeveloperProfile.php                            # Profil studio + rekening bank developer
│   │   ├── Platform.php                                    # Catalog produk (app/game); scope available() filter published+clean+!takedown+dev active
│   │   ├── Transaksi.php                                   # Transaksi purchase + upload_fee
│   │   ├── Wallet.php                                      # Saldo developer; method credit()/debit() atomik + lockForUpdate
│   │   ├── WalletTransaction.php                           # Mutasi credit/debit wallet
│   │   ├── Withdraw.php                                    # Permintaan pencairan dana ke IRIS
│   │   ├── Review.php                                      # Rating + komentar per produk
│   │   ├── ReviewHelpful.php                               # Helpful vote pada review
│   │   ├── Download.php                                    # Library user (produk yg dimiliki)
│   │   ├── ForumPost.php                                   # Post forum global (text + helpful_count)
│   │   └── ForumPostHelpful.php                            # Helpful vote forum post
│   ├── Providers/
│   │   └── AppServiceProvider.php                          # Service provider default Laravel
│   └── Services/
│       ├── MidtransService.php                             # Wrapper Snap (createSnapToken + handleNotification)
│       ├── MidtransPayoutService.php                       # Wrapper IRIS Disbursement (createPayout + status)
│       └── VirusTotalService.php                           # Wrapper VirusTotal API v3 (upload + getAnalysis)
│
├── config/
│   ├── dvnstore.php                                        # KONFIG UTAMA: upload_fee, platform_fee_percent, min_withdraw,
│   │                                                       # genres list (Game + Aplikasi), midtrans, iris (enabled flag),
│   │                                                       # virustotal, admin (seeder)
│   ├── app.php / auth.php / cache.php / database.php /
│   ├── filesystems.php / logging.php / mail.php /
│   ├── queue.php / services.php / session.php             # Konfig default Laravel (tidak diubah signifikan)
│
├── database/
│   ├── migrations/                                         # 16 file (11 schema baru + 2 patch + 3 default Laravel)
│   │   ├── 0001_01_01_000000_create_users_table.php       # Default Laravel — JANGAN DIUBAH
│   │   ├── 0001_01_01_000001_create_cache_table.php       # Default Laravel — JANGAN DIUBAH
│   │   ├── 0001_01_01_000002_create_jobs_table.php        # Default Laravel — JANGAN DIUBAH
│   │   ├── 2026_01_01_000001_create_penggunas_table.php           # role enum, status enum, blocked_at, blocked_reason
│   │   ├── 2026_01_01_000002_create_developer_profiles_table.php  # studio + bank
│   │   ├── 2026_01_01_000003_create_platforms_table.php           # catalog + scan_status + is_published + is_taken_down
│   │   ├── 2026_01_01_000004_create_transaksis_table.php          # tipe enum, snap_token, midtrans_order_id
│   │   ├── 2026_01_01_000005_create_wallets_table.php             # saldo per developer
│   │   ├── 2026_01_01_000006_create_wallet_transactions_table.php # mutasi credit/debit
│   │   ├── 2026_01_01_000007_create_withdraws_table.php           # bank_snapshot JSON, iris_payout_reference_no
│   │   ├── 2026_01_01_000008_create_reviews_table.php             # unique platform_id+user_id
│   │   ├── 2026_01_01_000009_create_review_helpfuls_table.php     # unique review_id+user_id
│   │   ├── 2026_01_01_000010_create_downloads_table.php           # unique user_id+platform_id, FK transaksi
│   │   ├── 2026_01_01_000011_create_forum_posts_table.php         # forum_posts + forum_post_helpfuls
│   │   ├── 2026_05_12_000001_add_unique_transaksi_to_wallet_transactions.php  # Anti double-credit
│   │   └── 2026_05_12_000002_add_pending_update_fields_to_platforms_table.php  # Tambah pending_file_* + file_updated_at (fitur update installer)
│   ├── seeders/
│   │   ├── DatabaseSeeder.php                              # Memanggil AdminSeeder
│   │   └── AdminSeeder.php                                 # Buat akun admin dari .env (ADMIN_NAME/EMAIL/PASSWORD)
│   └── factories/
│       └── UserFactory.php                                 # Default Laravel — bisa diabaikan
│
├── routes/
│   ├── web.php                                             # 75 route, 51 named. Group: public auth, role:user/developer/admin,
│   │                                                       # /payment/* untuk webhook (no auth)
│   └── console.php                                         # Default Laravel
│
├── resources/
│   ├── views/                                              # 46 blade view, sudah di-redesign UI/UX
│   │   ├── Layouts/
│   │   │   ├── layout.blade.php                            # Master layout: head, alert flash, content slot, scripts stack
│   │   │   └── Partials/
│   │   │       ├── Header.blade.php                        # Navbar dinamis per role (admin/developer/user)
│   │   │       └── Footer.blade.php                        # Footer minimal
│   │   ├── Components/
│   │   │   ├── carousel.blade.php                          # Hero carousel di dashboard utama
│   │   │   ├── category.blade.php                          # Cards Popular Categories (Apps + Games)
│   │   │   └── nav.blade.php                               # Nav tabs Home/Game/Aplikasi
│   │   ├── Auth/
│   │   │   ├── Login.blade.php                             # Form login email/password
│   │   │   ├── RegisterChoice.blade.php                    # Pilih daftar sebagai User atau Developer
│   │   │   ├── RegisterUser.blade.php                      # Form daftar customer (4 field)
│   │   │   ├── RegisterDeveloper.blade.php                 # Form daftar dev (akun + studio + bank)
│   │   │   ├── ForgetPassword.blade.php                    # Verifikasi email + kode unik
│   │   │   └── ForgetPassword1.blade.php                   # Set password baru
│   │   ├── User/
│   │   │   ├── DashboardUtama.blade.php                    # Landing user: carousel + popular games + popular apps
│   │   │   ├── DashboardGames.blade.php                    # List semua game
│   │   │   ├── DashboardApps.blade.php                     # List semua aplikasi
│   │   │   ├── TopGames.blade.php                          # Game sort by rating
│   │   │   ├── TopApps.blade.php                           # Aplikasi sort by rating
│   │   │   ├── AllGame.blade.php                           # Semua game
│   │   │   ├── AllApp.blade.php                            # Semua aplikasi
│   │   │   ├── Search.blade.php                            # Hasil pencarian (nama/genre)
│   │   │   ├── Lable.blade.php                             # Detail produk: icon, harga, rating, review, tombol beli/download
│   │   │   ├── Checkout.blade.php                          # Halaman bayar (load Midtrans Snap JS)
│   │   │   ├── PaymentFinish.blade.php                     # Halaman setelah kembali dari Midtrans
│   │   │   ├── Unduhan.blade.php                           # Library: list download user + tombol download file + badge "Update tersedia"
│   │   │   ├── Profile.blade.php                           # Lihat profil sendiri
│   │   │   └── EditProfile.blade.php                       # Edit nama, email, avatar
│   │   ├── Developer/
│   │   │   ├── Dashboard.blade.php                         # Stat cards: total produk, download, revenue, saldo wallet
│   │   │   ├── Wallet.blade.php                            # Saldo + history mutasi
│   │   │   ├── Platforms/
│   │   │   │   ├── Index.blade.php                         # List produk dev + status scan/publish/payment + badge pending update
│   │   │   │   ├── Create.blade.php                        # Form upload produk (icon, video, file installer)
│   │   │   │   ├── Edit.blade.php                          # Edit produk
│   │   │   │   ├── UploadFee.blade.php                     # Halaman bayar Rp 10.000 (Midtrans Snap)
│   │   │   │   └── UpdateFile.blade.php                    # Form upload file pengganti + monitoring status scan VirusTotal
│   │   │   └── Withdraws/
│   │   │       ├── Index.blade.php                         # Riwayat withdraw (saat ini disabled banner)
│   │   │       └── Create.blade.php                        # Form request withdraw
│   │   ├── Admin/
│   │   │   ├── Dashboard.blade.php                         # Stat global + 10 transaksi terakhir
│   │   │   ├── Users/
│   │   │   │   └── Index.blade.php                         # List user + block/unblock
│   │   │   ├── Developers/
│   │   │   │   ├── Index.blade.php                         # List dev + saldo + block/unblock
│   │   │   │   └── Show.blade.php                          # Detail dev: profil, produk, riwayat withdraw
│   │   │   ├── Platforms/
│   │   │   │   ├── Index.blade.php                         # List semua platform + takedown/restore
│   │   │   │   └── Show.blade.php                          # Detail platform: icon, scan result JSON, review
│   │   │   ├── Transactions/
│   │   │   │   ├── Index.blade.php                         # Semua transaksi (purchase + upload_fee)
│   │   │   │   └── Withdraws.blade.php                     # Semua withdraw + IRIS ref no
│   │   │   └── Forum/
│   │   │       └── Index.blade.php                         # Moderasi forum: hide/unhide/delete
│   │   ├── Forum/
│   │   │   └── Index.blade.php                             # Forum global (user+dev post, admin readonly)
│   │   └── Error/
│   │       ├── Forbidden403.blade.php                      # Halaman 403
│   │       └── Forbidden404.blade.php                      # Halaman 404
│   ├── css/
│   │   └── app.css                                         # Entry CSS Vite (default Laravel, tidak dipakai)
│   └── js/
│       ├── app.js                                          # Entry JS Vite (default)
│       └── bootstrap.js                                    # Axios CSRF default Laravel
│
└── public/
    ├── asset/css/                                          # Design system custom (1.726 baris CSS total)
    │   ├── layout.css                                      # Root tokens: --aqua, --dark-blue, --navy, --ink, --space-*, --shadow-*
    │   ├── dashboard.css                                   # Halaman dashboard (carousel, category cards, product cards)
    │   ├── auth.css                                        # Login + register pages
    │   ├── profile.css                                     # Halaman profil
    │   ├── editp.css                                       # Halaman edit profil
    │   ├── lable.css                                       # Halaman detail produk
    │   └── unduh.css                                       # Halaman library/unduhan
    ├── asset/image/                                        # Logo + asset gambar statis
    ├── asset/video/                                        # Asset video (jika ada)
    ├── storage/                                            # Symlink ke storage/app/public (php artisan storage:link)
    ├── index.php                                           # Entry point web server
    ├── favicon.ico
    ├── robots.txt
    └── .htaccess
```

## Ringkasan Angka
- **12 model** (Pengguna, DeveloperProfile, Platform, Transaksi, Wallet, WalletTransaction, Withdraw, Review, ReviewHelpful, Download, ForumPost, ForumPostHelpful)
- **19 controller** (Auth + 6 Admin + 4 Developer + 4 User + 2 Payment + ForumController + Controller base)
- **3 service** (Midtrans, MidtransPayout, VirusTotal) + **2 jobs** (ScanUploadedFileJob, ScanPendingUpdateJob)
- **1 middleware** custom (EnsureRole) + alias `role:` di `bootstrap/app.php`
- **16 migration** (11 schema baru + 2 patch + 3 default Laravel)
- **2 seeder** (DatabaseSeeder, AdminSeeder)
- **47 blade view** + **3 partial** (Layouts + Components)
- **78 route** (54 named), terkelompok per role
- **7 file CSS** (1.726 baris) sebagai design system

## Konvensi Path
- View: PascalCase folder + PascalCase file (mis. `User/Lable.blade.php`)
- Controller: PascalCase + suffix `Controller` (mis. `Admin/PlatformController.php`)
- Route name: `<role>.<resource>.<action>` (mis. `developer.platforms.upload-fee`)
- Migration: `YYYY_MM_DD_HHMMSS_*` (Laravel default)

## File yang TIDAK Boleh Diubah Tanpa Izin Eksplisit
- `bootstrap/app.php` (kecuali tambah middleware alias)
- `composer.lock`, `package-lock.json`
- `vendor/`, `storage/framework/`, `bootstrap/cache/`
- `database/migrations/0001_01_01_*` (default Laravel)
- `public/asset/css/*` dan semua `resources/views/**.blade.php` (UI sudah final)
