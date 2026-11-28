<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Report;
use App\Models\ReportResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityAndIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private Category $activeCategory;

    private User $superAdmin;

    private User $staffAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->activeCategory = Category::create([
            'name' => 'Akademik & Perkuliahan',
            'slug' => 'akademik-perkuliahan',
            'description' => 'Urusan akademik',
            'is_active' => true,
        ]);

        $this->superAdmin = User::create([
            'name' => 'Ketua Komisi',
            'email' => 'super@silira.ac.id',
            'role' => 'superadmin',
            'password' => 'password123',
        ]);

        $this->staffAdmin = User::create([
            'name' => 'Sekretariat',
            'email' => 'staff@silira.ac.id',
            'role' => 'admin_dpm',
            'password' => 'password123',
        ]);
    }

    public function test_staff_admin_cannot_create_or_delete_users_but_superadmin_can(): void
    {
        $this->actingAs($this->staffAdmin)
            ->post(route('admin.users.store'), [
                'name' => 'Hacker',
                'email' => 'hacker@silira.ac.id',
                'role' => 'superadmin',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertForbidden();

        $this->actingAs($this->staffAdmin)
            ->delete(route('admin.users.destroy', $this->superAdmin))
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $this->superAdmin->id]);

        $this->actingAs($this->superAdmin)
            ->post(route('admin.users.store'), [
                'name' => 'Pengurus Baru',
                'email' => 'baru@silira.ac.id',
                'role' => 'admin_dpm',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', ['email' => 'baru@silira.ac.id']);
    }

    public function test_stranger_cannot_reply_without_valid_pin(): void
    {
        $report = Report::create([
            'ticket_code' => 'SLR-2026-PUB999',
            'access_pin' => Hash::make('SECRET'),
            'jenis_laporan' => 'Aspirasi',
            'category_id' => $this->activeCategory->id,
            'privacy' => 'public',
            'nama_pelapor' => 'Mahasiswa Asli',
            'nim_nip' => '220401010',
            'no_wa' => '081234567890',
            'judul_laporan' => 'Aspirasi Terbuka',
            'isi_laporan' => 'Isi aspirasi terbuka yang panjangnya lebih dari dua puluh karakter.',
            'status' => 'diproses',
        ]);

        $this->post(route('tracking.reply', $report->ticket_code), [
            'access_pin' => 'WRONG1',
            'message' => 'Komentar palsu dari orang asing.',
        ])->assertSessionHasErrors('access_pin');

        $this->assertDatabaseMissing('report_responses', [
            'report_id' => $report->id,
            'message' => 'Komentar palsu dari orang asing.',
        ]);
    }

    public function test_confidential_report_requires_pin_and_masks_reporter_name(): void
    {
        $report = Report::create([
            'ticket_code' => 'SLR-2026-SEC001',
            'access_pin' => Hash::make('RAHAS1'),
            'jenis_laporan' => 'Pengaduan',
            'category_id' => $this->activeCategory->id,
            'privacy' => 'confidential',
            'nama_pelapor' => 'Nama Sangat Sensitif',
            'nim_nip' => '220401055',
            'no_wa' => '081234567891',
            'judul_laporan' => 'Laporan Etika Tertutup',
            'isi_laporan' => 'Kronologi kejadian sensitif yang hanya boleh diakses dengan PIN.',
            'status' => 'menunggu',
        ]);

        // Locked before PIN
        $this->get(route('tracking.show', $report->ticket_code))
            ->assertOk()
            ->assertSee('Verifikasi PIN akses tiket')
            ->assertDontSee('Laporan Etika Tertutup')
            ->assertDontSee('Nama Sangat Sensitif');

        // Direct visit to success page redirects to tracking
        $this->get(route('reports.success', $report->ticket_code))
            ->assertRedirect(route('tracking.show', ['ticketCode' => $report->ticket_code]));

        // Unlock with valid PIN
        $this->post(route('tracking.unlock', $report->ticket_code), [
            'access_pin' => 'RAHAS1',
        ])->assertRedirect(route('tracking.show', ['ticketCode' => $report->ticket_code]));

        // After unlock: content visible, real name still masked
        $this->get(route('tracking.show', $report->ticket_code))
            ->assertOk()
            ->assertSee('Laporan Etika Tertutup')
            ->assertSee('Pelapor Rahasia')
            ->assertDontSee('Nama Sangat Sensitif');
    }

    public function test_private_and_internal_attachments_are_protected_and_cleaned_on_delete(): void
    {
        Storage::fake('local');

        $reportPath = UploadedFile::fake()->create('rahasia.pdf', 50, 'application/pdf')->store('attachments', 'local');
        $internalPath = UploadedFile::fake()->create('notulen.pdf', 50, 'application/pdf')->store('attachments/responses', 'local');

        $report = Report::create([
            'ticket_code' => 'SLR-2026-ATT001',
            'access_pin' => Hash::make('PINATT'),
            'jenis_laporan' => 'Pengaduan',
            'category_id' => $this->activeCategory->id,
            'privacy' => 'confidential',
            'nama_pelapor' => 'Pelapor File',
            'nim_nip' => '220401077',
            'no_wa' => '081234567892',
            'judul_laporan' => 'Pengaduan dengan Lampiran',
            'isi_laporan' => 'Pengaduan sensitif disertai lampiran PDF rahasia.',
            'attachment_path' => $reportPath,
            'attachment_name' => 'rahasia.pdf',
            'status' => 'diproses',
        ]);

        $internalResponse = ReportResponse::create([
            'report_id' => $report->id,
            'user_id' => $this->superAdmin->id,
            'sender_type' => 'admin',
            'sender_name' => $this->superAdmin->name,
            'message' => 'Notulen internal',
            'attachment_path' => $internalPath,
            'attachment_name' => 'notulen.pdf',
            'is_internal' => true,
        ]);

        // Unauthenticated guest blocked from confidential attachment & internal note attachment
        $this->get(route('tracking.attachment', $report->ticket_code))->assertForbidden();
        $this->get(route('tracking.response.attachment', [$report->ticket_code, $internalResponse]))->assertForbidden();

        // Admin can download internal attachment
        $this->actingAs($this->superAdmin)
            ->get(route('admin.responses.attachment', $internalResponse))
            ->assertOk();

        // Deleting report removes both files from local disk
        $this->actingAs($this->superAdmin)
            ->delete(route('admin.reports.destroy', $report))
            ->assertRedirect(route('admin.reports.index'));

        Storage::disk('local')->assertMissing($reportPath);
        Storage::disk('local')->assertMissing($internalPath);
    }

    public function test_csv_export_neutralizes_formula_injection(): void
    {
        Report::create([
            'ticket_code' => 'SLR-2026-CSV001',
            'access_pin' => Hash::make('PINCSV'),
            'jenis_laporan' => 'Pengaduan',
            'category_id' => $this->activeCategory->id,
            'privacy' => 'public',
            'nama_pelapor' => '=CMD|"/C calc"!A0',
            'nim_nip' => '220401088',
            'no_wa' => '081234567893',
            'judul_laporan' => '+SUM(A1:A2)',
            'isi_laporan' => '@HYPERLINK("http://evil.example","klik")',
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.reports.export'));
        $response->assertOk();

        $csv = $response->streamedContent();
        $this->assertStringContainsString("'=CMD|", $csv);
        $this->assertStringContainsString("'+SUM(A1:A2)", $csv);
        $this->assertStringContainsString("'@HYPERLINK(", $csv);
    }

    public function test_category_slug_collision_returns_validation_error_instead_of_500(): void
    {
        $this->actingAs($this->superAdmin)
            ->post(route('admin.categories.store'), [
                'name' => 'Akademik - Perkuliahan',
                'description' => 'Slug sama dengan Akademik & Perkuliahan',
            ])
            ->assertSessionHasErrors('name');
    }

    public function test_inactive_category_is_rejected_on_public_submission(): void
    {
        $inactive = Category::create([
            'name' => 'Arsip Lama',
            'slug' => 'arsip-lama',
            'is_active' => false,
        ]);

        $this->post(route('reports.store'), [
            'jenis_laporan' => 'Aspirasi',
            'category_id' => $inactive->id,
            'privacy' => 'public',
            'nama_pelapor' => 'Budi',
            'nim_nip' => '220401001',
            'no_wa' => '081234567890',
            'judul_laporan' => 'Tes kategori nonaktif',
            'isi_laporan' => 'Isi laporan yang cukup panjang untuk lolos minimal dua puluh karakter.',
        ])->assertSessionHasErrors('category_id');
    }

    public function test_admin_login_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('admin.login.submit'), [
                'email' => 'super@silira.ac.id',
                'password' => 'wrong-password',
            ])->assertRedirect();
        }

        $this->post(route('admin.login.submit'), [
            'email' => 'super@silira.ac.id',
            'password' => 'wrong-password',
        ])->assertTooManyRequests();
    }

    public function test_public_navbar_hides_admin_login_for_guests(): void
    {
        $html = $this->get(route('home'))->assertOk()->getContent();

        preg_match('/<header.*?<\/header>/s', $html, $headerMatch);
        $this->assertNotEmpty($headerMatch);
        $this->assertStringNotContainsString(route('admin.login'), $headerMatch[0]);
    }

    public function test_public_aspiration_detail_is_read_only_and_blocks_confidential_reports(): void
    {
        $publicReport = Report::create([
            'ticket_code' => 'SLR-2026-ASP001',
            'access_pin' => Hash::make('PINASP'),
            'jenis_laporan' => 'Aspirasi',
            'category_id' => $this->activeCategory->id,
            'privacy' => 'public',
            'nama_pelapor' => 'Ahmad',
            'nim_nip' => '220401001',
            'no_wa' => '081234567890',
            'judul_laporan' => 'Perbaikan Fasilitas Kelas',
            'isi_laporan' => 'Mohon perbaikan proyektor kelas karena sudah redup sejak bulan lalu.',
            'status' => 'diproses',
        ]);

        $confidentialReport = Report::create([
            'ticket_code' => 'SLR-2026-ASP002',
            'access_pin' => Hash::make('PINSEC'),
            'jenis_laporan' => 'Pengaduan',
            'category_id' => $this->activeCategory->id,
            'privacy' => 'confidential',
            'nama_pelapor' => 'Nadia',
            'nim_nip' => '220401002',
            'no_wa' => '081234567891',
            'judul_laporan' => 'Aduan Rahasia Internal',
            'isi_laporan' => 'Isi aduan rahasia yang tidak boleh dibuka lewat direktori publik.',
            'status' => 'menunggu',
        ]);

        $this->get(route('public.aspirations.show', $publicReport->ticket_code))
            ->assertOk()
            ->assertSee('Perbaikan Fasilitas Kelas')
            ->assertSee('Tampilan transparansi publik (hanya baca)')
            ->assertDontSee('Tambah tanggapan atau bukti');

        $this->get(route('public.aspirations.show', $confidentialReport->ticket_code))
            ->assertNotFound();
    }

    public function test_web_responses_include_security_headers(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_switching_report_from_public_to_anonymous_masks_existing_reporter_replies(): void
    {
        $report = Report::create([
            'ticket_code' => 'SLR-2026-PRV001',
            'access_pin' => Hash::make('PINPRV'),
            'jenis_laporan' => 'Aspirasi',
            'category_id' => $this->activeCategory->id,
            'privacy' => 'public',
            'nama_pelapor' => 'Nama Asli Sangat Rahasia',
            'nim_nip' => '220401099',
            'no_wa' => '081234567899',
            'judul_laporan' => 'Aspirasi Awalnya Publik',
            'isi_laporan' => 'Isi aspirasi yang awalnya publik lalu diubah ke anonim.',
            'status' => 'diproses',
        ]);

        ReportResponse::create([
            'report_id' => $report->id,
            'sender_type' => 'reporter',
            'sender_name' => 'Nama Asli Sangat Rahasia',
            'message' => 'Klarifikasi tambahan dari pelapor.',
            'is_internal' => false,
        ]);

        $report->update(['privacy' => 'anonymous']);

        $this->get(route('tracking.show', $report->ticket_code))
            ->assertOk()
            ->assertSee('Pelapor Anonim')
            ->assertDontSee('Nama Asli Sangat Rahasia');

        $this->get(route('public.aspirations.show', $report->ticket_code))
            ->assertOk()
            ->assertSee('Pelapor Anonim')
            ->assertDontSee('Nama Asli Sangat Rahasia');
    }

    public function test_database_seeder_does_not_overwrite_existing_admin_password(): void
    {
        $customPassword = 'SuperStrongProductionPassword!2026';
        $existing = User::create([
            'name' => 'Existing Admin',
            'email' => 'admin@silira.ac.id',
            'role' => 'superadmin',
            'password' => $customPassword,
        ]);

        $this->seed();

        $existing->refresh();
        $this->assertTrue(Hash::check($customPassword, $existing->password));
        $this->assertFalse(Hash::check('password123', $existing->password));
    }
}
