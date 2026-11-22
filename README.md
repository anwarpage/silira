# Silira

**Sistem Integrasi Layanan Interaksi dan Respon Aspirasi** — portal layanan aspirasi, pengaduan, dan permintaan informasi mahasiswa untuk program kerja **Dewan Perwakilan Mahasiswa (DPM) Fakultas Dakwah dan Ilmu Komunikasi**.

---

## Latar Belakang & Riwayat Versi

- **`v1-php-native` (November 2024)** — Prototipe awal berbasis PHP Native, MySQLi, dan Bootstrap 4. Diarsipkan secara utuh pada Git tag [`v1-php-native`](../../releases/tag/v1-php-native).
- **`v2` (Laravel)** — Refactor menyeluruh ke Laravel untuk menghadirkan pelacakan kode tiket tanpa login, interaksi dua arah, kontrol privasi tiga tingkat, autentikasi pengurus, dan dokumentasi tindak lanjut lintas periode.

## Mengapa Tidak Sekadar Google Form?

Google Form hanya mengumpulkan data satu arah ke spreadsheet. Silira menyelesaikan empat kebutuhan inti pengelolaan aspirasi fakultas:

1. **Kepastian status via kode tiket** — Pelapor tidak perlu login dan selalu dapat memeriksa status laporannya (`Menunggu` → `Diverifikasi` → `Diproses` → `Selesai` / `Ditolak`).
2. **Interaksi dua arah** — Pelapor dapat mengirim klarifikasi atau bukti tambahan langsung pada halaman tiket yang sama.
3. **Privasi tiga tingkat** — Mendukung mode `Publik`, `Anonim` (nama disembunyikan dari publik), dan `Rahasia` (tidak tampil di feed publik).
4. **Akuntabilitas & estafet DPM** — Menyediakan catatan koordinasi internal terpisah dari publik, statistik transparansi, serta ekspor rekap sesuai filter.

---

## Tech Stack

- **Backend**: PHP 8.2+, Laravel 13, Eloquent ORM
- **Frontend**: Blade Templates, Tailwind CSS v4, Vite, Vanilla JavaScript
- **Database**: SQLite (default development) / MySQL 8+ (siap pakai melalui Laravel Migrations)
- **Testing**: PHPUnit (`Feature Tests`)

---

## Struktur Direktori Utama

```text
app/
├── Http/Controllers/
│   ├── HomeController.php              # Beranda & feed aspirasi publik
│   ├── ReportController.php            # Form kirim laporan & halaman kode tiket
│   ├── TrackingController.php          # Pelacakan kode tiket & balasan 2 arah pelapor
│   └── Admin/
│       ├── AuthController.php          # Login & logout pengurus DPM
│       ├── DashboardController.php     # Metrik & distribusi laporan
│       ├── ReportController.php        # CRUD, tindak lanjut, catatan internal, export CSV
│       ├── CategoryController.php      # Kelola kategori bidang
│       └── UserController.php          # Kelola akun pengurus & ganti kata sandi
└── Models/
    ├── Category.php
    ├── Report.php
    ├── ReportResponse.php
    └── User.php

database/
├── migrations/                         # Skema users, categories, reports, report_responses
└── seeders/DatabaseSeeder.php          # Data demo kategori, akun DPM, & contoh tiket

resources/views/
├── components/                         # brand-logo, select-dropdown
├── layouts/                            # public.blade.php, admin.blade.php
├── public/                             # home, create, success, track, feed
└── admin/                              # login, dashboard, reports/*, categories/*, users/*
```

---

## Skema Database

Aplikasi menggunakan empat tabel inti yang dikelola melalui `database/migrations/`:

### 1. `users`
Menyimpan akun pengurus DPM yang berhak mengakses `/admin`.

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | bigint (PK) | ID pengurus |
| `name` | string | Nama lengkap pengurus |
| `email` | string (unique) | Email login |
| `role` | string(30) | `superadmin` atau `admin_dpm` |
| `jabatan` | string(120), nullable | Komisi / jabatan di DPM |
| `password` | string | Hash bcrypt |

### 2. `categories`
Daftar bidang tujuan laporan yang dapat dikelola dari panel admin.

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | bigint (PK) | ID kategori |
| `name` | string(100), unique | Nama bidang |
| `slug` | string(120), unique | Slug unik |
| `description` | string(255), nullable | Deskripsi ruang lingkup |
| `is_active` | boolean | Status aktif di form publik |

### 3. `reports`
Menyimpan seluruh aspirasi, pengaduan, dan permintaan informasi mahasiswa.

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | bigint (PK) | ID laporan |
| `ticket_code` | string(32), unique | Kode tiket pelacakan (`SLR-YYYY-XXXXXX`) |
| `jenis_laporan` | enum | `Aspirasi`, `Pengaduan`, `Permintaan Informasi` |
| `category_id` | FK → `categories.id` | Bidang tujuan (`nullOnDelete`) |
| `privacy` | enum | `public`, `anonymous`, `confidential` |
| `nama_pelapor`, `nim_nip`, `jurusan_instansi`, `no_wa` | string | Identitas verifikasi pelapor |
| `judul_laporan` | string(255) | Ringkasan laporan |
| `isi_laporan` | text | Uraian lengkap laporan |
| `tanggal_kejadian`, `lokasi_kejadian` | date / string, nullable | Konteks kejadian (opsional) |
| `attachment_path`, `attachment_name` | string, nullable | Path & nama asli lampiran bukti |
| `status` | enum | `menunggu`, `diverifikasi`, `diproses`, `selesai`, `ditolak` |
| `verified_at`, `resolved_at` | timestamp, nullable | Cap waktu verifikasi & penyelesaian |

### 4. `report_responses`
Menyimpan timeline perubahan status, tanggapan resmi DPM, balasan pelapor, dan catatan internal.

| Kolom | Tipe | Keterangan |
| :--- | :--- | :--- |
| `id` | bigint (PK) | ID riwayat |
| `report_id` | FK → `reports.id` | Laporan terkait (`cascadeOnDelete`) |
| `user_id` | FK → `users.id`, nullable | Terisi jika pengirim adalah admin DPM |
| `sender_type` | enum | `admin`, `reporter`, `system` |
| `sender_name` | string(120) | Nama pengirim yang tampil di timeline |
| `message` | text | Isi tanggapan / log |
| `attachment_path`, `attachment_name` | string, nullable | Lampiran pendukung tanggapan |
| `status_from`, `status_to` | string(30), nullable | Transisi status bila ada |
| `is_internal` | boolean | `true` = catatan internal DPM (tidak tampil ke pelapor) |

### Relasi Utama
- `Category` `hasMany` `Report`
- `Report` `belongsTo` `Category`
- `Report` `hasMany` `ReportResponse`
- `User` `hasMany` `ReportResponse`

---

## Ringkasan Route

| Akses | Method & Path | Fungsi |
| :--- | :--- | :--- |
| Publik | `GET /` | Beranda, live preview tiket, & aspirasi terbaru |
| Publik | `GET /lapor`, `POST /lapor` | Formulir pengiriman laporan tanpa login |
| Publik | `GET /lapor/sukses/{ticketCode}` | Konfirmasi & bukti cetak kode tiket |
| Publik | `GET /lacak`, `GET /lacak/{ticketCode}` | Pelacakan status & timeline progres |
| Publik | `POST /lacak/{ticketCode}/reply` | Tanggapan dua arah dari pelapor |
| Publik | `GET /aspirasi` | Direktori aspirasi publik & filter |
| Admin | `GET /admin/login`, `POST /admin/login` | Autentikasi pengurus DPM |
| Admin | `GET /admin` | Dashboard metrik & distribusi |
| Admin | `GET /admin/laporan`, `GET /admin/laporan/{report}` | Manajemen, filter, & detail laporan |
| Admin | `POST /admin/laporan/{report}/respon` | Update status, respon resmi, / catatan internal |
| Admin | `GET /admin/laporan-export` | Export CSV (UTF-8 BOM) mengikuti filter aktif |
| Admin | `GET /admin/kategori`, `GET /admin/akun` | Pengelolaan kategori bidang & akun pengurus |

---

## Instalasi Lokal

### Prasyarat
- PHP 8.2+ (ekstensi `pdo_sqlite` / `pdo_mysql`, `fileinfo`, `mbstring`, `gd`)
- Composer 2+
- Node.js 20+ & npm

### Langkah Setup
```bash
git clone https://github.com/anwarpage/silira.git
cd silira

composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate --seed
php artisan storage:link

npm run build
php artisan serve
```

Aplikasi berjalan di `http://127.0.0.1:8000`.

### Beralih ke MySQL
Secara default pengembangan lokal menggunakan SQLite (`database/database.sqlite`). Untuk menggunakan MySQL:
1. Buat database kosong (misal `silira_db`).
2. Ubah konfigurasi di `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=silira_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Jalankan `php artisan migrate --seed`.

---

## Akun & Data Demo (Seeder)

### Akun Admin DPM (`/admin/login`)

| Peran | Email | Password |
| :--- | :--- | :--- |
| Super Admin (Komisi Aspirasi) | `admin@silira.ac.id` | `password123` |
| Admin DPM (Sekretariat) | `sekretariat@silira.ac.id` | `password123` |

### Contoh Kode Tiket (`/lacak`)

| Kode Tiket | Klasifikasi | Privasi | Status |
| :--- | :--- | :--- | :--- |
| `SLR-2026-FDIK01` | Pengaduan | Publik | Selesai |
| `SLR-2026-FDIK02` | Aspirasi | Anonim | Sedang Diproses |
| `SLR-2026-FDIK03` | Permintaan Informasi | Publik | Diverifikasi |
| `SLR-2026-FDIK04` | Pengaduan | Rahasia | Menunggu Verifikasi |

---

## Pengujian

Seluruh alur inti (pengiriman tanpa login, kode tiket, privasi, balasan dua arah, proteksi auth admin, catatan internal, dan export CSV) diuji pada `tests/Feature/SiliraWorkflowTest.php`:

```bash
php artisan test
```

---

## Kontribusi

Kontribusi untuk pengembangan fitur, perbaikan bug, maupun peningkatan dokumentasi sangat terbuka. Silakan baca panduan lengkapnya di [CONTRIBUTING.md](CONTRIBUTING.md).

## Lisensi

Proyek ini dirilis di bawah lisensi [MIT](LICENSE).
