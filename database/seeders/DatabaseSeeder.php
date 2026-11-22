<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Report;
use App\Models\ReportResponse;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@silira.ac.id'],
            [
                'name' => 'Ketua Komisi Aspirasi DPM',
                'role' => 'superadmin',
                'jabatan' => 'Komisi Aspirasi & Advokasi DPM FDIK',
                'password' => Hash::make('password123'),
            ]
        );

        $staffAdmin = User::updateOrCreate(
            ['email' => 'sekretariat@silira.ac.id'],
            [
                'name' => 'Sekretariat DPM FDIK',
                'role' => 'admin_dpm',
                'jabatan' => 'Sekretariat Umum DPM FDIK',
                'password' => Hash::make('password123'),
            ]
        );

        $categoryList = [
            ['name' => 'Akademik & Perkuliahan', 'description' => 'Jadwal kuliah, KRS, dosen, kurikulum, dan bimbingan skripsi.'],
            ['name' => 'Fasilitas & Sarana Prasarana', 'description' => 'Ruang kelas, laboratorium komunikasi, proyektor, AC, Wi-Fi, dan musala.'],
            ['name' => 'Kemahasiswaan & Ormawa', 'description' => 'Kegiatan HMJ, UKM, pendanaan proker, dan ruang kesekretariatan.'],
            ['name' => 'Layanan Administrasi', 'description' => 'Surat menyurat, legalisir, layanan tata usaha, dan pelayanan fakultas.'],
            ['name' => 'Keuangan & UKT', 'description' => 'Banding UKT, beasiswa, dan transparansi anggaran kemahasiswaan.'],
            ['name' => 'Etika & Keamanan Kampus', 'description' => 'Kenyamanan lingkungan belajar, parkir, serta pelaporan pelanggaran etika.'],
            ['name' => 'Lainnya', 'description' => 'Aspirasi atau pertanyaan umum di luar kategori di atas.'],
        ];

        $categories = [];
        foreach ($categoryList as $item) {
            $cat = Category::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'is_active' => true,
                ]
            );
            $categories[$item['name']] = $cat;
        }

        if (Report::count() > 0) {
            return;
        }

        $r1 = Report::create([
            'ticket_code' => 'SLR-2026-FDIK01',
            'jenis_laporan' => 'Pengaduan',
            'category_id' => $categories['Fasilitas & Sarana Prasarana']->id,
            'privacy' => 'public',
            'nama_pelapor' => 'Ahmad Fauzi',
            'nim_nip' => '220401045',
            'jurusan_instansi' => 'Komunikasi dan Penyiaran Islam (KPI)',
            'no_wa' => '081234567890',
            'judul_laporan' => 'Proyektor dan AC Ruang Kuliah B.204 Tidak Berfungsi',
            'isi_laporan' => 'Sejak awal pekan ini proyektor di Ruang B.204 Gedung Fakultas Dakwah tidak menyala dan 1 unit AC mati sehingga proses perkuliahan siang hari cukup terganggu. Mohon DPM dapat meneruskan ke bagian sarana prasarana fakultas.',
            'tanggal_kejadian' => now()->subDays(9)->toDateString(),
            'lokasi_kejadian' => 'Ruang Kuliah B.204, Lantai 2',
            'status' => 'selesai',
            'verified_at' => now()->subDays(8),
            'resolved_at' => now()->subDays(2),
            'created_at' => now()->subDays(9),
            'updated_at' => now()->subDays(2),
        ]);

        ReportResponse::create([
            'report_id' => $r1->id,
            'sender_type' => 'system',
            'sender_name' => 'Sistem SILIRA',
            'message' => 'Laporan berhasil diterima oleh sistem dan menunggu verifikasi DPM FDIK.',
            'status_to' => 'menunggu',
            'created_at' => now()->subDays(9),
        ]);

        ReportResponse::create([
            'report_id' => $r1->id,
            'user_id' => $staffAdmin->id,
            'sender_type' => 'admin',
            'sender_name' => $staffAdmin->name,
            'message' => 'Laporan telah diverifikasi. Tim Komisi Aspirasi segera meneruskan nota dinas ke Kasubag Umum & Sarpras FDIK.',
            'status_from' => 'menunggu',
            'status_to' => 'diverifikasi',
            'created_at' => now()->subDays(8),
        ]);

        ReportResponse::create([
            'report_id' => $r1->id,
            'user_id' => $superAdmin->id,
            'sender_type' => 'admin',
            'sender_name' => $superAdmin->name,
            'message' => 'Teknisi fakultas telah menjadwalkan penggantian kabel HDMI proyektor dan servis AC pada hari Kamis.',
            'status_from' => 'diverifikasi',
            'status_to' => 'diproses',
            'created_at' => now()->subDays(5),
        ]);

        ReportResponse::create([
            'report_id' => $r1->id,
            'sender_type' => 'reporter',
            'sender_name' => $r1->nama_pelapor,
            'message' => 'Terima kasih infonya, hari ini sudah dicek teknisi dan proyektornya mulai diganti.',
            'created_at' => now()->subDays(3),
        ]);

        ReportResponse::create([
            'report_id' => $r1->id,
            'user_id' => $superAdmin->id,
            'sender_type' => 'admin',
            'sender_name' => $superAdmin->name,
            'message' => 'Perbaikan proyektor dan AC di Ruang B.204 telah selesai dan diverifikasi langsung oleh tim DPM. Terima kasih atas partisipasi Anda.',
            'status_from' => 'diproses',
            'status_to' => 'selesai',
            'created_at' => now()->subDays(2),
        ]);

        $r2 = Report::create([
            'ticket_code' => 'SLR-2026-FDIK02',
            'jenis_laporan' => 'Aspirasi',
            'category_id' => $categories['Akademik & Perkuliahan']->id,
            'privacy' => 'anonymous',
            'nama_pelapor' => 'Siti Rahmawati',
            'nim_nip' => '230402019',
            'jurusan_instansi' => 'Bimbingan Penyuluhan Islam (BPI)',
            'no_wa' => '085211223344',
            'judul_laporan' => 'Usulan Perpanjangan Jam Layanan Perpustakaan Fakultas Menjelang UAS',
            'isi_laporan' => 'Menjelang pekan UAS dan penyusunan proposal skripsi, banyak mahasiswa membutuhkan referensi cetak di perpustakaan fakultas setelah jam 16.00. Kami mengusulkan perpanjangan jam buka hingga pukul 19.00 selama 2 minggu menjelang ujian.',
            'lokasi_kejadian' => 'Perpustakaan Fakultas Dakwah dan Ilmu Komunikasi',
            'status' => 'diproses',
            'verified_at' => now()->subDays(4),
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(2),
        ]);

        ReportResponse::create([
            'report_id' => $r2->id,
            'sender_type' => 'system',
            'sender_name' => 'Sistem SILIRA',
            'message' => 'Aspirasi diterima dengan mode Anonim. Identitas pelapor disembunyikan dari publik.',
            'status_to' => 'menunggu',
            'created_at' => now()->subDays(5),
        ]);

        ReportResponse::create([
            'report_id' => $r2->id,
            'user_id' => $superAdmin->id,
            'sender_type' => 'admin',
            'sender_name' => $superAdmin->name,
            'message' => 'Aspirasi ini telah kami rangkum untuk dibawa dalam rapat audiensi dengan Wakil Dekan I Bidang Akademik pekan ini.',
            'status_from' => 'menunggu',
            'status_to' => 'diproses',
            'created_at' => now()->subDays(2),
        ]);

        ReportResponse::create([
            'report_id' => $r2->id,
            'user_id' => $superAdmin->id,
            'sender_type' => 'admin',
            'sender_name' => $superAdmin->name,
            'message' => 'Catatan internal: sudah dijadwalkan audiensi bersama WD I hari Rabu pukul 10.00.',
            'is_internal' => true,
            'created_at' => now()->subDays(2),
        ]);

        $r3 = Report::create([
            'ticket_code' => 'SLR-2026-FDIK03',
            'jenis_laporan' => 'Permintaan Informasi',
            'category_id' => $categories['Keuangan & UKT']->id,
            'privacy' => 'public',
            'nama_pelapor' => 'Rizky Pratama',
            'nim_nip' => '210403088',
            'jurusan_instansi' => 'Manajemen Dakwah (MD)',
            'no_wa' => '082199887766',
            'judul_laporan' => 'Jadwal dan Persyaratan Pengajuan Penurunan/Banding UKT Semester Genap',
            'isi_laporan' => 'Mohon informasi resmi mengenai lini masa dan berkas persyaratan pengajuan peninjauan ulang UKT untuk mahasiswa semester akhir yang tinggal mengambil skripsi.',
            'status' => 'diverifikasi',
            'verified_at' => now()->subDay(),
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDay(),
        ]);

        ReportResponse::create([
            'report_id' => $r3->id,
            'sender_type' => 'system',
            'sender_name' => 'Sistem SILIRA',
            'message' => 'Permintaan informasi berhasil tercatat di sistem SILIRA.',
            'status_to' => 'menunggu',
            'created_at' => now()->subDays(2),
        ]);

        ReportResponse::create([
            'report_id' => $r3->id,
            'user_id' => $staffAdmin->id,
            'sender_type' => 'admin',
            'sender_name' => $staffAdmin->name,
            'message' => 'Permintaan informasi telah diverifikasi. Kami sedang mengonfirmasi edaran resmi dari Bagian Akademik & Kemahasiswaan.',
            'status_from' => 'menunggu',
            'status_to' => 'diverifikasi',
            'created_at' => now()->subDay(),
        ]);

        $r4 = Report::create([
            'ticket_code' => 'SLR-2026-FDIK04',
            'jenis_laporan' => 'Pengaduan',
            'category_id' => $categories['Etika & Keamanan Kampus']->id,
            'privacy' => 'confidential',
            'nama_pelapor' => 'Nadia Kusuma',
            'nim_nip' => '230404012',
            'jurusan_instansi' => 'Pengembangan Masyarakat Islam (PMI)',
            'no_wa' => '081355667788',
            'judul_laporan' => 'Penerangan Area Parkir Belakang Gedung C Sangat Minim di Malam Hari',
            'isi_laporan' => 'Lampu taman dan area parkir belakang Gedung C padam sejak minggu lalu sehingga rawan bagi mahasiswa yang pulang dari kegiatan organisasi pada malam hari. Mohon segera ditindaklanjuti secara tertutup.',
            'lokasi_kejadian' => 'Parkir Belakang Gedung C',
            'status' => 'menunggu',
            'created_at' => now()->subHours(6),
            'updated_at' => now()->subHours(6),
        ]);

        ReportResponse::create([
            'report_id' => $r4->id,
            'sender_type' => 'system',
            'sender_name' => 'Sistem SILIRA',
            'message' => 'Laporan Rahasia diterima. Laporan tidak ditampilkan pada feed publik dan hanya dapat diakses melalui kode tiket serta Admin DPM.',
            'status_to' => 'menunggu',
            'created_at' => now()->subHours(6),
        ]);
    }
}
