<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiliraWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'Fasilitas & Sarana Prasarana',
            'slug' => 'fasilitas-dan-sarana-prasarana',
            'description' => 'Sarana perkuliahan',
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Admin DPM',
            'email' => 'admin@silira.ac.id',
            'role' => 'superadmin',
            'jabatan' => 'Komisi Aspirasi',
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_public_user_can_submit_report_without_login_and_get_ticket_code(): void
    {
        Storage::fake('public');

        $response = $this->post(route('reports.store'), [
            'jenis_laporan' => 'Pengaduan',
            'category_id' => $this->category->id,
            'privacy' => 'public',
            'nama_pelapor' => 'Budi Santoso',
            'nim_nip' => '220401099',
            'jurusan_instansi' => 'KPI',
            'no_wa' => '081234567890',
            'judul_laporan' => 'Lampu Kelas B.101 Mati Total',
            'isi_laporan' => 'Mohon perhatiannya karena lampu di ruang kelas B.101 mati total sejak kemarin sore.',
            'lokasi_kejadian' => 'Ruang B.101',
            'attachment' => UploadedFile::fake()->image('bukti.jpg'),
        ]);

        $report = Report::first();
        $this->assertNotNull($report);
        $this->assertStringStartsWith('SLR-', $report->ticket_code);
        $this->assertEquals('menunggu', $report->status);
        $this->assertNotNull($report->attachment_path);
        Storage::disk('public')->assertExists($report->attachment_path);

        $response->assertRedirect(route('reports.success', ['ticketCode' => $report->ticket_code]));
        $this->get(route('reports.success', ['ticketCode' => $report->ticket_code]))
            ->assertOk()
            ->assertSee($report->ticket_code);
    }

    public function test_privacy_modes_are_enforced_on_public_feed(): void
    {
        Report::create([
            'ticket_code' => 'SLR-2026-ANON01',
            'jenis_laporan' => 'Aspirasi',
            'category_id' => $this->category->id,
            'privacy' => 'anonymous',
            'nama_pelapor' => 'Nama Rahasia Anonim',
            'nim_nip' => '220401001',
            'no_wa' => '081200000001',
            'judul_laporan' => 'Judul Aspirasi Anonim',
            'isi_laporan' => 'Isi aspirasi anonim yang cukup panjang untuk validasi.',
            'status' => 'menunggu',
        ]);

        Report::create([
            'ticket_code' => 'SLR-2026-CONF01',
            'jenis_laporan' => 'Pengaduan',
            'category_id' => $this->category->id,
            'privacy' => 'confidential',
            'nama_pelapor' => 'Pelapor Rahasia',
            'nim_nip' => '220401002',
            'no_wa' => '081200000002',
            'judul_laporan' => 'Judul Laporan Sangat Rahasia',
            'isi_laporan' => 'Isi laporan rahasia yang tidak boleh muncul di feed publik.',
            'status' => 'menunggu',
        ]);

        $feed = $this->get(route('public.feed'));
        $feed->assertOk();
        $feed->assertSee('Judul Aspirasi Anonim');
        $feed->assertSee('Pelapor Anonim');
        $feed->assertDontSee('Nama Rahasia Anonim');
        $feed->assertDontSee('Judul Laporan Sangat Rahasia');
    }

    public function test_reporter_can_track_and_send_two_way_reply_without_login(): void
    {
        $report = Report::create([
            'ticket_code' => 'SLR-2026-TRK001',
            'jenis_laporan' => 'Aspirasi',
            'category_id' => $this->category->id,
            'privacy' => 'public',
            'nama_pelapor' => 'Rina Putri',
            'nim_nip' => '230401015',
            'no_wa' => '081299998888',
            'judul_laporan' => 'Usulan Penambahan Stop Kontak di Gazebo',
            'isi_laporan' => 'Mahasiswa sering mengerjakan tugas di gazebo fakultas namun colokan listrik sangat terbatas.',
            'status' => 'diproses',
        ]);

        $this->post(route('tracking.lookup'), ['ticket_code' => 'slr-2026-trk001'])
            ->assertRedirect(route('tracking.show', ['ticketCode' => 'SLR-2026-TRK001']));

        $this->post(route('tracking.reply', ['ticketCode' => $report->ticket_code]), [
            'message' => 'Sebagai tambahan, gazebo barat juga lampunya redup.',
        ])->assertRedirect(route('tracking.show', ['ticketCode' => $report->ticket_code]));

        $this->get(route('tracking.show', ['ticketCode' => $report->ticket_code]))
            ->assertOk()
            ->assertSee('Sebagai tambahan, gazebo barat juga lampunya redup.');
    }

    public function test_admin_routes_require_authentication_and_support_full_workflow(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));

        $report = Report::create([
            'ticket_code' => 'SLR-2026-ADM001',
            'jenis_laporan' => 'Pengaduan',
            'category_id' => $this->category->id,
            'privacy' => 'public',
            'nama_pelapor' => 'Dedi Kurniawan',
            'nim_nip' => '210401077',
            'no_wa' => '081377776666',
            'judul_laporan' => 'Wi-Fi Lantai 3 Sering Putus',
            'isi_laporan' => 'Koneksi Wi-Fi mahasiswa di lantai 3 gedung fakultas sering terputus saat jam sibuk.',
            'status' => 'menunggu',
        ]);

        // Login as Admin
        $this->post(route('admin.login.submit'), [
            'email' => 'admin@silira.ac.id',
            'password' => 'password123',
        ])->assertRedirect(route('admin.dashboard'));

        // Post internal note (must not appear publicly)
        $this->post(route('admin.reports.respond', $report), [
            'status' => 'diproses',
            'message' => 'Catatan rahasia internal DPM saja.',
            'is_internal' => '1',
        ])->assertRedirect(route('admin.reports.show', $report));

        $report->refresh();
        $this->assertEquals('menunggu', $report->status);

        // Post official response & update status to selesai
        $this->post(route('admin.reports.respond', $report), [
            'status' => 'selesai',
            'message' => 'Access point lantai 3 telah diganti oleh tim IT fakultas.',
        ])->assertRedirect(route('admin.reports.show', $report));

        $report->refresh();
        $this->assertEquals('selesai', $report->status);

        // Public tracking page shows official reply, hides internal note
        $this->get(route('tracking.show', $report->ticket_code))
            ->assertOk()
            ->assertSee('Access point lantai 3 telah diganti oleh tim IT fakultas.')
            ->assertDontSee('Catatan rahasia internal DPM saja.');

        // Export CSV works
        $export = $this->get(route('admin.reports.export', ['status' => 'selesai']));
        $export->assertOk();
        $this->assertStringContainsString('SLR-2026-ADM001', $export->streamedContent());
    }
}
