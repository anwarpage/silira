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

    public function test_public_user_can_submit_report_without_login_and_get_ticket_code_and_pin(): void
    {
        Storage::fake('local');

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
        $this->assertNotNull($report->access_pin);
        $this->assertEquals('menunggu', $report->status);
        $this->assertNotNull($report->attachment_path);
        Storage::disk('local')->assertExists($report->attachment_path);

        $response->assertRedirect(route('reports.success', ['ticketCode' => $report->ticket_code]));
        $response->assertSessionHas('submitted_ticket', $report->ticket_code);
        $response->assertSessionHas('submitted_pin');

        $pin = session('submitted_pin');
        $this->assertTrue($report->verifyAccessPin($pin));

        $this->get(route('reports.success', ['ticketCode' => $report->ticket_code]))
            ->assertOk()
            ->assertSee($report->ticket_code)
            ->assertSee($pin);

        $pdfResponse = $this->get(route('reports.receipt', ['ticketCode' => $report->ticket_code]));
        $pdfResponse->assertOk();
        $pdfResponse->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $pdfResponse->getContent());
    }

    public function test_privacy_modes_are_enforced_on_public_feed(): void
    {
        Report::create([
            'ticket_code' => 'SLR-2026-ANON01',
            'access_pin' => Hash::make('PIN123'),
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
            'access_pin' => Hash::make('PIN456'),
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

    public function test_reporter_can_track_and_send_two_way_reply_with_valid_pin(): void
    {
        $report = Report::create([
            'ticket_code' => 'SLR-2026-TRK001',
            'access_pin' => Hash::make('FDIK26'),
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
            'access_pin' => 'FDIK26',
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
            'access_pin' => Hash::make('FDIK26'),
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
        $this->assertNotNull($report->verified_at);
        $this->assertNotNull($report->resolved_at);

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

    public function test_closed_ticket_rejects_new_reporter_replies(): void
    {
        $report = Report::create([
            'ticket_code' => 'SLR-2026-CLS001',
            'access_pin' => Hash::make('FDIK26'),
            'jenis_laporan' => 'Pengaduan',
            'category_id' => $this->category->id,
            'privacy' => 'public',
            'nama_pelapor' => 'Budi',
            'nim_nip' => '220401001',
            'no_wa' => '081234567890',
            'judul_laporan' => 'Tiket Sudah Selesai',
            'isi_laporan' => 'Isi laporan yang sudah selesai ditangani sepenuhnya.',
            'status' => 'selesai',
            'resolved_at' => now(),
        ]);

        $this->post(route('tracking.reply', $report->ticket_code), [
            'access_pin' => 'FDIK26',
            'message' => 'Mencoba membalas tiket yang sudah ditutup.',
        ])->assertSessionHas('error');

        $this->assertDatabaseMissing('report_responses', [
            'report_id' => $report->id,
            'message' => 'Mencoba membalas tiket yang sudah ditutup.',
        ]);
    }

    public function test_admin_can_edit_report_reopen_status_and_update_own_password(): void
    {
        $report = Report::create([
            'ticket_code' => 'SLR-2026-EDT001',
            'access_pin' => Hash::make('FDIK26'),
            'jenis_laporan' => 'Pengaduan',
            'category_id' => $this->category->id,
            'privacy' => 'public',
            'nama_pelapor' => 'Andi',
            'nim_nip' => '220401012',
            'no_wa' => '081234567890',
            'judul_laporan' => 'Judul Awal',
            'isi_laporan' => 'Isi laporan awal yang sudah sempat ditutup lalu dibuka kembali.',
            'status' => 'selesai',
            'verified_at' => now()->subDay(),
            'resolved_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->put(route('admin.reports.update', $report), [
                'jenis_laporan' => 'Pengaduan',
                'category_id' => $this->category->id,
                'privacy' => 'public',
                'status' => 'diproses',
                'nama_pelapor' => 'Andi',
                'nim_nip' => '220401012',
                'no_wa' => '081234567890',
                'judul_laporan' => 'Judul Diperbarui',
                'isi_laporan' => 'Isi laporan awal yang sudah sempat ditutup lalu dibuka kembali.',
            ])
            ->assertRedirect(route('admin.reports.show', $report));

        $report->refresh();
        $this->assertEquals('Judul Diperbarui', $report->judul_laporan);
        $this->assertEquals('diproses', $report->status);
        $this->assertNull($report->resolved_at);

        $this->actingAs($this->admin)
            ->put(route('admin.users.password'), [
                'current_password' => 'password123',
                'password' => 'NewPassword!2026',
                'password_confirmation' => 'NewPassword!2026',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->admin->refresh();
        $this->assertTrue(Hash::check('NewPassword!2026', $this->admin->password));
    }
}
