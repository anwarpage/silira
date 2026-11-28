<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\ReportAnalyticsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly ReportAnalyticsService $analytics
    ) {}

    public function index(Request $request): View
    {
        $filters = $this->analytics->extractFilters($request);
        $payload = $this->analytics->buildSummaryPayload($filters);
        $activeTab = in_array($request->input('tab'), ['summary', 'question', 'individual'], true)
            ? (string) $request->input('tab')
            : 'summary';

        return view('admin.dashboard', [
            'filters' => $payload['filters'],
            'hasActiveFilters' => $payload['hasActiveFilters'],
            'periodLabel' => $payload['periodLabel'],
            'periodOptions' => ReportAnalyticsService::PERIOD_OPTIONS,
            'categories' => $payload['categories'],
            'summary' => $payload['summary'],
            'recentReports' => $payload['recentReports'],
            'activeTab' => $activeTab,
            'avgVerifyLabel' => $this->analytics->formatDuration($payload['summary']['stats']['avg_verify_hours']),
            'avgResolveLabel' => $this->analytics->formatDuration($payload['summary']['stats']['avg_resolve_hours']),
        ]);
    }

    public function questionsData(Request $request): JsonResponse
    {
        $filters = $this->analytics->extractFilters($request);

        return response()->json([
            'questions' => $this->analytics->buildQuestionPayload($filters),
        ]);
    }

    public function individualData(Request $request): JsonResponse
    {
        $filters = $this->analytics->extractFilters($request);
        $page = max(1, (int) $request->input('page', 1));
        $includeIndex = $request->boolean('with_index');

        return response()->json(
            $this->analytics->buildIndividualPayload($filters, $page, $includeIndex)
        );
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $filters = $this->analytics->extractFilters($request);
        $query = $this->analytics->filteredQuery($filters)
            ->with(['category'])
            ->withCount('responses')
            ->latest();

        $filename = 'analitik-respons-silira-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Kode Tiket',
                'Waktu Masuk',
                'Jenis Laporan',
                'Kategori Bidang',
                'Privasi',
                'Status',
                'Nama Pelapor',
                'NIM/NIP',
                'Jurusan/Instansi',
                'No WhatsApp',
                'Judul Laporan',
                'Isi Laporan',
                'Tanggal Kejadian',
                'Lokasi Kejadian',
                'Lampiran',
                'Waktu Verifikasi',
                'Waktu Selesai',
                'Jumlah Tanggapan',
            ]);

            foreach ($query->cursor() as $report) {
                fputcsv($handle, array_map([$this->analytics, 'sanitizeSpreadsheetCell'], [
                    $report->ticket_code,
                    $report->created_at?->format('Y-m-d H:i:s') ?? '-',
                    $report->jenis_laporan,
                    $report->category?->name ?? '-',
                    $report->privacy_label,
                    $report->status_label,
                    $report->nama_pelapor,
                    $report->nim_nip,
                    $report->jurusan_instansi ?? '-',
                    $report->no_wa,
                    $report->judul_laporan,
                    $report->isi_laporan,
                    $report->tanggal_kejadian?->format('Y-m-d') ?? '-',
                    $report->lokasi_kejadian ?? '-',
                    $report->attachment_name ?? '-',
                    $report->verified_at?->format('Y-m-d H:i:s') ?? '-',
                    $report->resolved_at?->format('Y-m-d H:i:s') ?? '-',
                    (string) $report->responses_count,
                ]));
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportExcel(Request $request): StreamedResponse
    {
        $filters = $this->analytics->extractFilters($request);
        $payload = $this->analytics->buildSummaryPayload($filters);
        $summary = $payload['summary'];
        $periodLabel = $payload['periodLabel'];
        $query = $this->analytics->filteredQuery($filters)
            ->with('category:id,name')
            ->withCount('responses')
            ->latest();

        $filename = 'analitik-silira-'.now()->format('Ymd-His').'.xls';

        return response()->streamDownload(function () use ($summary, $query, $periodLabel): void {
            $this->streamSpreadsheetXml($summary, $query, $periodLabel);
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    public function exportPdf(Request $request): Response
    {
        $filters = $this->analytics->extractFilters($request);
        $payload = $this->analytics->buildPdfExportPayload($filters, 25);

        $pdf = Pdf::loadView('pdf.analytics-report', [
            'summary' => $payload['summary'],
            'periodLabel' => $payload['periodLabel'],
            'hasActiveFilters' => $payload['hasActiveFilters'],
            'filters' => $payload['filters'],
            'categories' => $payload['categories'],
            'avgVerifyLabel' => $this->analytics->formatDuration($payload['summary']['stats']['avg_verify_hours']),
            'avgResolveLabel' => $this->analytics->formatDuration($payload['summary']['stats']['avg_resolve_hours']),
            'reports' => $payload['reports'],
            'generatedAt' => now()->format('d M Y, H:i'),
            'generatedBy' => $request->user()?->name ?? 'Admin DPM',
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-analitik-silira-'.now()->format('Ymd-His').'.pdf';

        return $pdf->download($filename);
    }

    public function exportIndividualPdf(Report $report): Response
    {
        $report->load(['category', 'responses' => fn ($q) => $q->orderBy('created_at')]);

        $pdf = Pdf::loadView('pdf.individual-response', [
            'report' => $report,
            'printedAt' => now()->format('d M Y, H:i'),
        ])->setPaper('a4', 'portrait');

        return $pdf->download("respons-{$report->ticket_code}.pdf");
    }

    /**
     * Stream a multi-sheet Excel 2003 SpreadsheetML (.xls) document with Summary + Raw Responses.
     *
     * @param  array<string, mixed>  $summary
     * @param  Builder<Report>  $query
     */
    private function streamSpreadsheetXml(array $summary, $query, string $periodLabel): void
    {
        $out = fopen('php://output', 'w');

        $e = fn (mixed $v): string => htmlspecialchars(
            $this->analytics->sanitizeSpreadsheetCell($v),
            ENT_XML1 | ENT_QUOTES,
            'UTF-8'
        );

        $cell = fn (mixed $v, string $style = 'Default', string $type = 'String'): string => '<Cell ss:StyleID="'.$style.'"><Data ss:Type="'.$type.'">'.($type === 'Number' ? (is_numeric($v) ? $v : 0) : $e($v)).'</Data></Cell>';

        fwrite($out, '<?xml version="1.0" encoding="UTF-8"?>'
            .'<?mso-application progid="Excel.Sheet"?>'
            .'<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"'
            .' xmlns:o="urn:schemas-microsoft-com:office:office"'
            .' xmlns:x="urn:schemas-microsoft-com:office:excel"'
            .' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">'
            .'<Styles>'
            .'<Style ss:ID="Default"><Font ss:FontName="Calibri" ss:Size="10" ss:Color="#0f172a"/><Alignment ss:Vertical="Center"/></Style>'
            .'<Style ss:ID="Title"><Font ss:FontName="Calibri" ss:Size="13" ss:Bold="1" ss:Color="#1e3a8a"/></Style>'
            .'<Style ss:ID="Muted"><Font ss:FontName="Calibri" ss:Size="9" ss:Color="#475569"/></Style>'
            .'<Style ss:ID="Section"><Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#1e3a8a"/><Interior ss:Color="#eff6ff" ss:Pattern="Solid"/></Style>'
            .'<Style ss:ID="Header"><Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#ffffff"/><Interior ss:Color="#0f172a" ss:Pattern="Solid"/></Style>'
            .'</Styles>'
            .'<Worksheet ss:Name="Ringkasan Analitik"><Table>'
            .'<Column ss:Width="240"/><Column ss:Width="110"/><Column ss:Width="100"/>');

        fwrite($out, '<Row>'.$cell('LAPORAN ANALITIK SILIRA — DPM FDIK', 'Title').'</Row>');
        fwrite($out, '<Row>'.$cell('Periode: '.$periodLabel.' | Diunduh: '.now()->format('d M Y, H:i').' WIB', 'Muted').'</Row>');
        fwrite($out, '<Row/>');
        fwrite($out, '<Row>'.$cell('RINGKASAN UTAMA', 'Section').$cell('', 'Section').'</Row>');
        fwrite($out, '<Row>'.$cell('Metrik', 'Header').$cell('Nilai', 'Header').'</Row>');

        $kpiRows = [
            ['Total Respons Masuk', $summary['stats']['total']],
            ['Menunggu Verifikasi', $summary['stats']['menunggu']],
            ['Diverifikasi & Diproses', $summary['stats']['active']],
            ['Selesai / Tuntas', $summary['stats']['selesai']],
            ['Ditolak', $summary['stats']['ditolak']],
            ['Tingkat Penyelesaian (%)', $summary['stats']['completion_rate'].'%'],
            ['Rata-rata Waktu Verifikasi', $this->analytics->formatDuration($summary['stats']['avg_verify_hours'])],
            ['Rata-rata Waktu Penyelesaian', $this->analytics->formatDuration($summary['stats']['avg_resolve_hours'])],
            ['Laporan dengan Lampiran', $summary['stats']['with_attachment'].' ('.$summary['stats']['with_attachment_rate'].'%)'],
        ];
        foreach ($kpiRows as [$label, $val]) {
            $type = is_int($val) ? 'Number' : 'String';
            fwrite($out, '<Row>'.$cell($label).$cell($val, 'Default', $type).'</Row>');
        }

        $writeBreakdown = function (string $title, array $items) use ($out, $cell): void {
            fwrite($out, '<Row/>');
            fwrite($out, '<Row>'.$cell($title, 'Section').$cell('', 'Section').$cell('', 'Section').'</Row>');
            fwrite($out, '<Row>'.$cell('Opsi', 'Header').$cell('Jumlah', 'Header').$cell('Persentase', 'Header').'</Row>');
            foreach ($items as $item) {
                fwrite($out, '<Row>'
                    .$cell($item['label'])
                    .$cell((int) $item['count'], 'Default', 'Number')
                    .$cell($item['percent'].'%')
                    .'</Row>');
            }
        };

        $writeBreakdown('DISTRIBUSI JENIS LAPORAN', $summary['jenis']);
        $writeBreakdown('DISTRIBUSI KATEGORI BIDANG', $summary['categories']);
        $writeBreakdown('DISTRIBUSI TINGKAT PRIVASI', $summary['privacy']);
        $writeBreakdown('DISTRIBUSI STATUS PENANGANAN', $summary['status']);
        $writeBreakdown('DISTRIBUSI JURUSAN / INSTANSI (TOP 8)', $summary['jurusan']);

        fwrite($out, '</Table></Worksheet>'
            .'<Worksheet ss:Name="Data Respons"><Table>'
            .'<Column ss:Width="120"/><Column ss:Width="115"/><Column ss:Width="115"/><Column ss:Width="160"/>'
            .'<Column ss:Width="80"/><Column ss:Width="110"/><Column ss:Width="140"/><Column ss:Width="100"/>'
            .'<Column ss:Width="160"/><Column ss:Width="110"/><Column ss:Width="220"/><Column ss:Width="320"/>'
            .'<Column ss:Width="95"/><Column ss:Width="160"/><Column ss:Width="95"/>');

        $headers = [
            'Kode Tiket', 'Waktu Masuk', 'Jenis Laporan', 'Kategori Bidang',
            'Privasi', 'Status', 'Nama Pelapor', 'NIM/NIP', 'Jurusan/Instansi',
            'No WhatsApp', 'Judul Laporan', 'Isi Laporan', 'Tanggal Kejadian',
            'Lokasi Kejadian', 'Jumlah Tanggapan',
        ];
        fwrite($out, '<Row>'.implode('', array_map(fn ($h) => $cell($h, 'Header'), $headers)).'</Row>');

        foreach ($query->cursor() as $report) {
            fwrite($out, '<Row>'
                .$cell($report->ticket_code)
                .$cell($report->created_at?->format('Y-m-d H:i') ?? '-')
                .$cell($report->jenis_laporan)
                .$cell($report->category?->name ?? '-')
                .$cell($report->privacy_label)
                .$cell($report->status_label)
                .$cell($report->nama_pelapor)
                .$cell($report->nim_nip)
                .$cell($report->jurusan_instansi ?? '-')
                .$cell($report->no_wa)
                .$cell($report->judul_laporan)
                .$cell($report->isi_laporan)
                .$cell($report->tanggal_kejadian?->format('Y-m-d') ?? '-')
                .$cell($report->lokasi_kejadian ?? '-')
                .$cell((int) $report->responses_count, 'Default', 'Number')
                .'</Row>');
        }

        fwrite($out, '</Table></Worksheet></Workbook>');
        fclose($out);
    }
}
