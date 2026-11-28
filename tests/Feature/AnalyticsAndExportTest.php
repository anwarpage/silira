<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AnalyticsAndExportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Category $catFasilitas;

    private Category $catAkademik;

    private Report $reportA;

    private Report $reportB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Analitik',
            'email' => 'analitik@silira.ac.id',
            'role' => 'superadmin',
            'jabatan' => 'Komisi Aspirasi',
            'password' => Hash::make('password123'),
        ]);

        $this->catFasilitas = Category::create([
            'name' => 'Fasilitas & Sarana Prasarana',
            'slug' => 'fasilitas',
            'is_active' => true,
        ]);

        $this->catAkademik = Category::create([
            'name' => 'Akademik & Perkuliahan',
            'slug' => 'akademik',
            'is_active' => true,
        ]);

        $this->reportA = Report::create([
            'ticket_code' => 'SLR-2026-ANL001',
            'access_pin' => Hash::make('FDIK26'),
            'jenis_laporan' => 'Pengaduan',
            'category_id' => $this->catFasilitas->id,
            'privacy' => 'public',
            'nama_pelapor' => '=CMD("calc")',
            'nim_nip' => '220401001',
            'jurusan_instansi' => 'KPI',
            'no_wa' => '081234567890',
            'judul_laporan' => 'AC Ruang B.204 Mati',
            'isi_laporan' => 'Pendingin ruangan tidak menyala sejak pagi.',
            'lokasi_kejadian' => 'Ruang B.204',
            'status' => 'selesai',
            'verified_at' => now()->subHours(10),
            'resolved_at' => now()->subHours(2),
            'created_at' => now()->subHours(12),
        ]);

        $this->reportB = Report::create([
            'ticket_code' => 'SLR-2026-ANL002',
            'access_pin' => Hash::make('FDIK26'),
            'jenis_laporan' => 'Aspirasi',
            'category_id' => $this->catAkademik->id,
            'privacy' => 'anonymous',
            'nama_pelapor' => 'Siti Aminah',
            'nim_nip' => '230402002',
            'jurusan_instansi' => 'BPI',
            'no_wa' => '085211223344',
            'judul_laporan' => 'Perpanjangan Jam Perpustakaan',
            'isi_laporan' => 'Mohon jam buka perpustakaan diperpanjang menjelang UAS.',
            'lokasi_kejadian' => 'Perpustakaan FDIK',
            'status' => 'menunggu',
            'created_at' => now()->subHours(4),
        ]);
    }

    public function test_guest_cannot_access_analytics_export_endpoints(): void
    {
        $this->get(route('admin.analytics.questions'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.analytics.individual'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.analytics.export.csv'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.analytics.export.excel'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.analytics.export.pdf'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.analytics.response.pdf', $this->reportA))->assertRedirect(route('admin.login'));
    }

    public function test_lazy_loaded_question_and_individual_json_endpoints_work_with_filters(): void
    {
        $questionsRes = $this->actingAs($this->admin)
            ->getJson(route('admin.analytics.questions', ['jenis' => 'Pengaduan']));

        $questionsRes->assertOk()
            ->assertJsonStructure(['questions' => [['key', 'title', 'type', 'groups']]])
            ->assertJsonPath('questions.0.total_responses', 1);

        $individualRes = $this->actingAs($this->admin)
            ->getJson(route('admin.analytics.individual', ['page' => 1, 'with_index' => 1]));

        $individualRes->assertOk()
            ->assertJsonPath('total', 2)
            ->assertJsonPath('page', 1)
            ->assertJsonPath('item.ticket_code', 'SLR-2026-ANL002');
        $this->assertCount(2, $individualRes->json('tickets'));
    }

    public function test_dashboard_renders_google_forms_tabs_and_respects_filters(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard', ['jenis' => 'Pengaduan']));

        $response->assertOk();
        $response->assertSee('Dashboard &amp; Analitik', false);
        $response->assertSee('Ringkasan');
        $response->assertSee('Per Pertanyaan');
        $response->assertSee('Individual');
        $response->assertSee('SLR-2026-ANL001');
        $response->assertDontSee('SLR-2026-ANL002');
    }

    public function test_analytics_csv_export_filters_and_sanitizes_formulas(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.analytics.export.csv', ['status' => 'selesai']));

        $response->assertOk();
        $content = $response->streamedContent();

        $this->assertStringContainsString('SLR-2026-ANL001', $content);
        $this->assertStringNotContainsString('SLR-2026-ANL002', $content);
        $this->assertStringContainsString("'=CMD", $content);
    }

    public function test_analytics_excel_export_contains_summary_and_data_sheets(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.analytics.export.excel', ['category_id' => $this->catAkademik->id]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.ms-excel; charset=UTF-8');

        $body = $response->streamedContent();
        $this->assertStringContainsString('ss:Name="Ringkasan Analitik"', $body);
        $this->assertStringContainsString('ss:Name="Data Respons"', $body);
        $this->assertStringContainsString('SLR-2026-ANL002', $body);
        $this->assertStringNotContainsString('SLR-2026-ANL001', $body);
    }

    public function test_analytics_handles_empty_results_and_reversed_custom_date_range(): void
    {
        $emptyRes = $this->actingAs($this->admin)
            ->get(route('admin.dashboard', ['status' => 'ditolak']));
        $emptyRes->assertOk();

        $reversedRes = $this->actingAs($this->admin)
            ->get(route('admin.dashboard', [
                'period' => 'custom',
                'date_from' => now()->addDay()->toDateString(),
                'date_to' => now()->subDays(2)->toDateString(),
            ]));
        $reversedRes->assertOk();
        $reversedRes->assertSee('SLR-2026-ANL001');
        $reversedRes->assertSee('SLR-2026-ANL002');
    }

    public function test_analytics_pdf_and_individual_response_pdf_download_valid_pdfs(): void
    {
        $summaryPdf = $this->actingAs($this->admin)
            ->get(route('admin.analytics.export.pdf', ['period' => '30d']));

        $summaryPdf->assertOk();
        $summaryPdf->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $summaryPdf->getContent());

        $individualPdf = $this->actingAs($this->admin)
            ->get(route('admin.analytics.response.pdf', $this->reportA));

        $individualPdf->assertOk();
        $individualPdf->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $individualPdf->getContent());
    }
}
