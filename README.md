# SILIRA

**Sistem Integrasi Layanan Interaksi dan Respon Aspirasi** — portal pengelolaan aspirasi, pengaduan, dan permintaan informasi mahasiswa untuk program kerja Dewan Perwakilan Mahasiswa (DPM) Fakultas Dakwah dan Ilmu Komunikasi.

## Latar Belakang & Evolusi

- **`v1-php-native` (November 2024)**: Prototipe awal berbasis PHP Native, MySQLi, dan Bootstrap 4. Diarsipkan pada tag Git [`v1-php-native`](../../releases/tag/v1-php-native).
- **`v2` (Laravel)**: Refactor penuh untuk menghadirkan pelacakan kode tiket tanpa login, interaksi dua arah, kontrol privasi tiga tingkat, autentikasi pengurus, dan arsip laporan lintas periode.

## Mengapa Tidak Sekadar Google Form?

Google Form hanya mengumpulkan data satu arah ke spreadsheet. SILIRA menyelesaikan empat keterbatasan utamanya:

1. **Pelacakan status berbasis kode tiket** — pelapor tidak perlu login dan selalu tahu progres laporannya (`Menunggu` → `Diverifikasi` → `Diproses` → `Selesai`/`Ditolak`).
2. **Interaksi dua arah** — pelapor dapat mengirim klarifikasi atau bukti tambahan langsung pada tiket yang sama.
3. **Kontrol privasi terstruktur** — mendukung mode `Publik`, `Anonim` (identitas disembunyikan), dan `Rahasia` (tidak tampil di feed publik).
4. **Akuntabilitas & catatan internal** — pengurus DPM memiliki catatan koordinasi internal yang terpisah dari tanggapan publik serta ekspor rekap sesuai filter.

## Ringkasan Fitur

- **Portal Publik**: Beranda ringkas + statistik transparansi, formulir laporan dengan upload bukti (JPG/PNG/WEBP/PDF maks. 5 MB), bukti kode tiket cetak/salin, halaman pelacakan & balasan dua arah, serta feed aspirasi publik dengan filter.
- **Portal Admin DPM**: Dashboard metrik & distribusi bidang, daftar laporan dengan pencarian & filter, tindak lanjut status + tanggapan resmi/catatan internal, tautan WhatsApp langsung ke pelapor, edit/hapus laporan, ekspor CSV (UTF-8 BOM untuk Excel), serta manajemen kategori dan akun pengurus.

## Instalasi Lokal

Prasyarat: PHP 8.2+, Composer, Node.js & npm.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

Secara default aplikasi menggunakan SQLite agar langsung berjalan. Untuk menggunakan MySQL, ubah `DB_CONNECTION=mysql` beserta kredensial database di `.env`, lalu jalankan `php artisan migrate --seed`.

## Data & Akun Demo (Seeder)

### Akun Admin DPM (`/admin/login`)

| Peran | Email | Password |
| :--- | :--- | :--- |
| Super Admin (Komisi Aspirasi) | `admin@silira.ac.id` | `password123` |
| Admin DPM (Sekretariat) | `sekretariat@silira.ac.id` | `password123` |

### Contoh Kode Tiket (`/lacak`)

| Kode Tiket | Jenis | Privasi | Status |
| :--- | :--- | :--- | :--- |
| `SLR-2026-FDIK01` | Pengaduan | Publik | Selesai |
| `SLR-2026-FDIK02` | Aspirasi | Anonim | Sedang Diproses |
| `SLR-2026-FDIK03` | Permintaan Informasi | Publik | Diverifikasi |
| `SLR-2026-FDIK04` | Pengaduan | Rahasia | Menunggu Verifikasi |

## Pengujian

```bash
php artisan test
```
