# SILIRA — Sistem Integrasi Layanan Interaksi dan Respon Aspirasi

Portal layanan aspirasi, pengaduan, dan permintaan informasi mahasiswa **Fakultas Dakwah dan Ilmu Komunikasi (FDIK)** yang dikembangkan untuk program kerja **Dewan Perwakilan Mahasiswa (DPM)**, terinspirasi dari [LAPOR! (lapor.go.id)](https://lapor.go.id/).

> **Riwayat Evolusi Project**
> - **v1 (`v1-php-native`, November 2024)**: Prototipe awal berbasis PHP Native + MySQLi + Bootstrap 4. Tetap diarsipkan secara utuh pada Git tag [`v1-php-native`](../../releases/tag/v1-php-native).
> - **v2 (Laravel Refactor)**: Pengembangan lanjutan menggunakan **Laravel**, **Tailwind CSS**, sistem **Kode Tiket Pelacakan tanpa login**, **Opsi Privasi (`Publik`, `Anonim`, `Rahasia`)**, **Interaksi 2 Arah**, dan **Dashboard Admin DPM**.

---

## Alur & Fitur Utama

### Portal Publik (Tanpa Login)
- **Formulir Aspirasi & Pengaduan (`/lapor`)**: Klasifikasi (`Aspirasi`, `Pengaduan`, `Permintaan Informasi`), kategori bidang fakultas, opsi privasi (`Publik`, `Anonim`, `Rahasia`), lokasi/tanggal kejadian, serta upload lampiran bukti (JPG/PNG/WEBP/PDF maks. 5 MB).
- **Kode Tiket Unik (`SLR-YYYY-XXXXXX`)**: Dihasilkan otomatis setelah pelaporan, lengkap dengan tombol salin kode dan cetak bukti lapor.
- **Lacak Status & Interaksi 2 Arah (`/lacak`)**: Pelapor memantau progres status (`Menunggu` → `Diverifikasi` → `Diproses` → `Selesai`/`Ditolak`), membaca tanggapan resmi DPM, serta mengirim klarifikasi atau bukti tambahan langsung pada tiket tanpa perlu membuat akun.
- **Beranda & Feed Transparansi (`/`, `/aspirasi`)**: Statistik transparansi real-time dan daftar aspirasi publik (laporan `Rahasia` tidak dipublikasikan, laporan `Anonim` menyembunyikan nama pelapor).

### Portal Admin DPM (`/admin`)
- **Autentikasi Pengurus**: Login terproteksi session & bcrypt hashing.
- **Dashboard Statistik**: Ringkasan jumlah laporan per status, distribusi kategori bidang, dan distribusi klasifikasi.
- **Manajemen & Tindak Lanjut Laporan**: Pencarian, filter (status, kategori, bulan), pembaruan status + tanggapan resmi, **Catatan Internal DPM** (tidak terlihat pelapor), tombol cepat hubungi pelapor via WhatsApp, edit, dan hapus laporan.
- **Export CSV/Excel**: Mengunduh rekap laporan sesuai filter yang sedang aktif (UTF-8 BOM, siap buka di Microsoft Excel).
- **Kelola Kategori Bidang & Akun Pengurus DPM**.

---

## Cara Menjalankan di Lokal

### Prasyarat
- PHP 8.2+ (dengan ekstensi `pdo_sqlite` / `pdo_mysql`, `fileinfo`, `mbstring`)
- Composer
- Node.js & npm

### Langkah Instalasi
```bash
# 1. Install dependensi PHP & Frontend
composer install
npm install

# 2. Salin konfigurasi environment & generate app key
cp .env.example .env
php artisan key:generate

# 3. Migrasi database & isi data demo (default menggunakan SQLite)
php artisan migrate --seed

# 4. Buat symlink storage untuk lampiran bukti
php artisan storage:link

# 5. Build aset frontend & jalankan server lokal
npm run build
php artisan serve
```

> **Ingin menggunakan MySQL?** Cukup ubah `DB_CONNECTION=mysql` beserta `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` pada file `.env`, lalu jalankan `php artisan migrate --seed`.

### Akun Demo Admin DPM (Seeder)
- **URL Login**: `http://127.0.0.1:8000/admin/login`
- **Email**: `admin@silira.ac.id`
- **Password**: `password123`

### Contoh Kode Tiket Demo (Seeder)
- `SLR-2026-FDIK01` (Pengaduan — Selesai, dengan timeline lengkap)
- `SLR-2026-FDIK02` (Aspirasi Anonim — Sedang Diproses)
- `SLR-2026-FDIK03` (Permintaan Informasi — Diverifikasi)
- `SLR-2026-FDIK04` (Pengaduan Rahasia — Menunggu Verifikasi)

---

## Pengujian Otomatis
```bash
php artisan test
```
