# Panduan Kontribusi Silira

Terima kasih atas ketertarikan Anda untuk berkontribusi pada pengembangan **Silira**. Dokumen ini menjelaskan alur kerja, standar kode, dan persyaratan sebelum mengajukan Pull Request.

---

## 1. Alur Pengembangan

1. **Fork** repositori ini ke akun GitHub Anda, lalu clone ke mesin lokal:
   ```bash
   git clone https://github.com/<username-anda>/silira.git
   cd silira
   ```
2. **Buat branch baru** dari `main` dengan nama yang deskriptif:
   - Fitur baru: `feat/nama-fitur` (contoh: `feat/notifikasi-whatsapp`)
   - Perbaikan bug: `fix/nama-bug` (contoh: `fix/filter-bulan-export`)
   - Dokumentasi: `docs/topik` (contoh: `docs/panduan-deploy`)
3. **Siapkan environment lokal**:
   ```bash
   composer install
   npm install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   ```
4. Lakukan perubahan, tambahkan pengujian yang relevan, lalu pastikan seluruh pengujian lulus sebelum commit.

---

## 2. Konvensi Commit

Gunakan format Conventional Commits agar riwayat perubahan mudah dibaca:

- `feat:` penambahan fitur baru
- `fix:` perbaikan bug
- `refactor:` penyempurnaan struktur kode tanpa mengubah perilaku utama
- `docs:` pembaruan dokumentasi (`README.md`, `CONTRIBUTING.md`, dsb.)
- `test:` penambahan atau pembaruan test
- `chore:` pemeliharaan dependensi atau konfigurasi

Contoh:
```text
feat(tracking): tambahkan batas ukuran unggahan lampiran pada balasan pelapor
fix(admin): perbaiki filter kategori saat export CSV
```

---

## 3. Standar Kode & Pengujian

Sebelum membuka Pull Request, pastikan langkah berikut berjalan tanpa error:

```bash
# 1. Format kode PHP menggunakan Laravel Pint
./vendor/bin/pint

# 2. Build aset frontend (Tailwind CSS & Vite)
npm run build

# 3. Jalankan seluruh suite pengujian
php artisan test
```

### Hal yang Perlu Diperhatikan
- **Keamanan**: Gunakan Eloquent/Query Builder (jangan interpolasi variabel mentah ke SQL), validasi semua input melalui `$request->validate(...)`, dan jaga proteksi middleware `auth` pada seluruh route `/admin/*`.
- **Privasi**: Pastikan laporan berstatus `confidential` tidak pernah bocor ke route publik (`/`, `/aspirasi`), laporan `anonymous` tidak menampilkan `nama_pelapor` / `nim_nip` ke publik, dan `report_responses` dengan `is_internal = true` tidak pernah tampil di halaman `/lacak`.
- **Migrasi**: Setiap perubahan skema tabel wajib dibuat lewat file migration baru di `database/migrations/` (jangan mengedit migration lama yang sudah dirilis).

---

## 4. Checklist Pull Request

- [ ] Perubahan berjalan bersih di lokal (`php artisan serve` + `npm run build`).
- [ ] Seluruh test lulus (`php artisan test`).
- [ ] Tidak menyertakan file sensitif (`.env`, `database/database.sqlite`, atau berkas upload pengguna).
- [ ] Deskripsi PR menjelaskan *apa* yang diubah dan *mengapa* perubahan tersebut diperlukan.
