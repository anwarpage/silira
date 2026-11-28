<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Report;
use App\Models\ReportResponse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ReportAnalyticsService
{
    public const PERIOD_OPTIONS = [
        'all' => 'Semua Waktu',
        '7d' => '7 Hari Terakhir',
        '30d' => '30 Hari Terakhir',
        '90d' => '90 Hari Terakhir',
        'year' => 'Tahun Ini',
        'custom' => 'Rentang Kustom',
    ];

    /**
     * Extract and normalize filter values from the request.
     *
     * @return array{period: string, date_from: ?string, date_to: ?string, category_id: ?int, jenis: ?string, privacy: ?string, status: ?string}
     */
    public function extractFilters(Request $request): array
    {
        $period = (string) $request->input('period', 'all');
        if (! array_key_exists($period, self::PERIOD_OPTIONS)) {
            $period = 'all';
        }

        $dateFrom = $this->normalizeDate($request->input('date_from'));
        $dateTo = $this->normalizeDate($request->input('date_to'));

        if ($dateFrom && $dateTo && $dateFrom > $dateTo) {
            [$dateFrom, $dateTo] = [$dateTo, $dateFrom];
        }

        if (($dateFrom || $dateTo) && $period === 'all') {
            $period = 'custom';
        }

        $categoryId = $request->filled('category_id') ? (int) $request->input('category_id') : null;
        $jenis = in_array($request->input('jenis'), Report::JENIS_OPTIONS, true) ? (string) $request->input('jenis') : null;
        $privacy = array_key_exists((string) $request->input('privacy'), Report::PRIVACY_OPTIONS) ? (string) $request->input('privacy') : null;
        $status = array_key_exists((string) $request->input('status'), Report::STATUSES) ? (string) $request->input('status') : null;

        return [
            'period' => $period,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'category_id' => $categoryId,
            'jenis' => $jenis,
            'privacy' => $privacy,
            'status' => $status,
        ];
    }

    /**
     * Build a filtered Eloquent query for reports.
     *
     * @param  array<string, mixed>  $filters
     */
    public function filteredQuery(array $filters): Builder
    {
        $query = Report::query();

        [$start, $end] = $this->resolveDateRange($filters);

        if ($start) {
            $query->where('created_at', '>=', $start);
        }

        if ($end) {
            $query->where('created_at', '<=', $end);
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['jenis'])) {
            $query->where('jenis_laporan', $filters['jenis']);
        }

        if (! empty($filters['privacy'])) {
            $query->where('privacy', $filters['privacy']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query;
    }

    /**
     * SQL-aggregated summary payload for dashboard render & exports (O(1) memory regardless of table size).
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function buildSummaryPayload(array $filters, int $highlightLimit = 12): array
    {
        $categories = Category::query()->select(['id', 'name'])->orderBy('name')->get();
        $baseQuery = $this->filteredQuery($filters);

        $highlights = (clone $baseQuery)
            ->select([
                'id',
                'ticket_code',
                'jenis_laporan',
                'category_id',
                'privacy',
                'status',
                'nama_pelapor',
                'nim_nip',
                'judul_laporan',
                'lokasi_kejadian',
                'created_at',
            ])
            ->with('category:id,name')
            ->withCount('responses')
            ->latest()
            ->take($highlightLimit)
            ->get();

        return [
            'filters' => $filters,
            'hasActiveFilters' => $this->hasActiveFilters($filters),
            'periodLabel' => $this->describePeriod($filters),
            'categories' => $categories,
            'recentReports' => $highlights->take(8),
            'summary' => $this->buildSummaryFromDatabase($baseQuery, $categories, $filters, $highlights),
        ];
    }

    /**
     * Lazy-loaded Google Forms Question-by-Question breakdown.
     *
     * @param  array<string, mixed>  $filters
     * @return array<int, array<string, mixed>>
     */
    public function buildQuestionPayload(array $filters): array
    {
        $reports = $this->filteredQuery($filters)
            ->select([
                'id',
                'ticket_code',
                'jenis_laporan',
                'category_id',
                'privacy',
                'status',
                'nama_pelapor',
                'jurusan_instansi',
                'judul_laporan',
                'tanggal_kejadian',
                'lokasi_kejadian',
                'created_at',
            ])
            ->with('category:id,name')
            ->latest()
            ->get();

        return $this->buildQuestionBreakdown($reports);
    }

    /**
     * Server-paginated single-response payload for Google Forms Individual tab.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function buildIndividualPayload(array $filters, int $page = 1, bool $includeIndex = false): array
    {
        $baseQuery = $this->filteredQuery($filters);
        $total = (clone $baseQuery)->count();

        if ($total === 0) {
            return [
                'total' => 0,
                'page' => 1,
                'item' => null,
                'tickets' => $includeIndex ? [] : null,
            ];
        }

        $safePage = max(1, min($page, $total));

        $report = (clone $baseQuery)
            ->with(['category:id,name', 'responses' => fn ($q) => $q->orderBy('created_at')])
            ->latest()
            ->skip($safePage - 1)
            ->take(1)
            ->first();

        $tickets = null;
        if ($includeIndex) {
            $tickets = (clone $baseQuery)
                ->select(['id', 'ticket_code', 'judul_laporan'])
                ->latest()
                ->take(200)
                ->get()
                ->values()
                ->map(fn (Report $r, int $idx) => [
                    'page' => $idx + 1,
                    'ticket_code' => $r->ticket_code,
                    'judul_laporan' => $r->judul_laporan,
                ])
                ->all();
        }

        return [
            'total' => $total,
            'page' => $safePage,
            'item' => $report ? $this->formatIndividualReport($report) : null,
            'tickets' => $tickets,
        ];
    }

    /**
     * Bounded payload for PDF summary export (SQL-aggregated summary + top N recent rows).
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function buildPdfExportPayload(array $filters, int $recentLimit = 25): array
    {
        $payload = $this->buildSummaryPayload($filters, max(12, $recentLimit));
        $payload['reports'] = $this->filteredQuery($filters)
            ->select([
                'id',
                'ticket_code',
                'jenis_laporan',
                'category_id',
                'privacy',
                'status',
                'nama_pelapor',
                'nim_nip',
                'judul_laporan',
                'created_at',
            ])
            ->with('category:id,name')
            ->latest()
            ->take($recentLimit)
            ->get();

        return $payload;
    }

    /**
     * @param  Collection<int, Category>  $categories
     * @param  array<string, mixed>  $filters
     * @param  Collection<int, Report>  $highlights
     * @return array<string, mixed>
     */
    private function buildSummaryFromDatabase(Builder $baseQuery, Collection $categories, array $filters, Collection $highlights): array
    {
        $agg = (clone $baseQuery)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN attachment_path IS NOT NULL AND attachment_path != '' THEN 1 ELSE 0 END) as with_attachment")
            ->selectRaw('AVG(CASE WHEN verified_at IS NOT NULL THEN GREATEST(0, TIMESTAMPDIFF(MINUTE, created_at, verified_at)) / 60.0 END) as avg_verify_hours')
            ->selectRaw("AVG(CASE WHEN resolved_at IS NOT NULL AND status = 'selesai' THEN GREATEST(0, TIMESTAMPDIFF(MINUTE, created_at, resolved_at)) / 60.0 END) as avg_resolve_hours")
            ->selectRaw('MIN(created_at) as min_created_at, MAX(created_at) as max_created_at')
            ->first();

        $total = (int) ($agg->total ?? 0);
        $denom = max(1, $total);

        $rawStatusCounts = (clone $baseQuery)
            ->selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $statusCounts = [];
        foreach (array_keys(Report::STATUSES) as $key) {
            $statusCounts[$key] = (int) ($rawStatusCounts[$key] ?? 0);
        }

        $completionRate = $total > 0 ? round(($statusCounts['selesai'] / $total) * 100, 1) : 0.0;
        $avgVerifyHours = $agg?->avg_verify_hours !== null ? round((float) $agg->avg_verify_hours, 1) : null;
        $avgResolveHours = $agg?->avg_resolve_hours !== null ? round((float) $agg->avg_resolve_hours, 1) : null;
        $withAttachment = (int) ($agg->with_attachment ?? 0);

        $totalResponses = $total > 0
            ? ReportResponse::query()
                ->whereIn('report_id', (clone $baseQuery)->select('id'))
                ->count()
            : 0;

        // 1. Jenis Laporan
        $jenisCounts = (clone $baseQuery)
            ->selectRaw('jenis_laporan, COUNT(*) as aggregate')
            ->groupBy('jenis_laporan')
            ->pluck('aggregate', 'jenis_laporan');

        $jenisPalette = ['#1e3a8a', '#0284c7', '#0f766e'];
        $jenisItems = [];
        foreach (Report::JENIS_OPTIONS as $idx => $jenis) {
            $count = (int) ($jenisCounts[$jenis] ?? 0);
            $jenisItems[] = [
                'label' => $jenis,
                'count' => $count,
                'percent' => $total > 0 ? round(($count / $denom) * 100, 1) : 0.0,
                'color' => $jenisPalette[$idx % count($jenisPalette)],
            ];
        }

        // 2. Kategori Bidang
        $categoryCounts = (clone $baseQuery)
            ->selectRaw('category_id, COUNT(*) as aggregate')
            ->groupBy('category_id')
            ->pluck('aggregate', 'category_id');

        $categoryItems = $categories->map(function (Category $cat) use ($categoryCounts, $total, $denom) {
            $count = (int) ($categoryCounts[$cat->id] ?? 0);

            return [
                'id' => $cat->id,
                'label' => $cat->name,
                'count' => $count,
                'percent' => $total > 0 ? round(($count / $denom) * 100, 1) : 0.0,
            ];
        })->sortByDesc('count')->values()->all();

        // 3. Privasi
        $privacyCounts = (clone $baseQuery)
            ->selectRaw('privacy, COUNT(*) as aggregate')
            ->groupBy('privacy')
            ->pluck('aggregate', 'privacy');

        $privacyMeta = [
            'public' => ['label' => 'Publik', 'color' => '#1e3a8a'],
            'anonymous' => ['label' => 'Anonim', 'color' => '#d97706'],
            'confidential' => ['label' => 'Rahasia', 'color' => '#334155'],
        ];
        $privacyItems = [];
        foreach ($privacyMeta as $key => $meta) {
            $count = (int) ($privacyCounts[$key] ?? 0);
            $privacyItems[] = [
                'key' => $key,
                'label' => $meta['label'],
                'count' => $count,
                'percent' => $total > 0 ? round(($count / $denom) * 100, 1) : 0.0,
                'color' => $meta['color'],
            ];
        }

        // 4. Status Penanganan
        $statusColors = [
            'menunggu' => '#d97706',
            'diverifikasi' => '#0284c7',
            'diproses' => '#1e3a8a',
            'selesai' => '#15803d',
            'ditolak' => '#b91c1c',
        ];
        $statusItems = [];
        foreach (Report::STATUSES as $key => $label) {
            $count = $statusCounts[$key];
            $statusItems[] = [
                'key' => $key,
                'label' => $label,
                'count' => $count,
                'percent' => $total > 0 ? round(($count / $denom) * 100, 1) : 0.0,
                'color' => $statusColors[$key] ?? '#475569',
            ];
        }

        // 5. Jurusan / Instansi (Top 8)
        $jurusanItems = (clone $baseQuery)
            ->selectRaw("COALESCE(NULLIF(TRIM(jurusan_instansi), ''), 'Tidak diisi') as label, COUNT(*) as aggregate")
            ->groupBy('label')
            ->orderByDesc('aggregate')
            ->limit(8)
            ->get()
            ->map(fn ($row) => [
                'label' => (string) $row->label,
                'count' => (int) $row->aggregate,
                'percent' => $total > 0 ? round(((int) $row->aggregate / $denom) * 100, 1) : 0.0,
            ])
            ->values()
            ->all();

        // 6. Timeline
        $timeline = $this->buildTimelineFromDatabase(
            $baseQuery,
            $filters,
            $agg?->min_created_at,
            $agg?->max_created_at
        );

        // 7. Weekday distribution (MySQL WEEKDAY: 0=Mon..6=Sun -> +1 => 1=Mon..7=Sun)
        $dayNames = [1 => 'Sen', 2 => 'Sel', 3 => 'Rab', 4 => 'Kam', 5 => 'Jum', 6 => 'Sab', 7 => 'Min'];
        $weekdayCounts = array_fill_keys(array_keys($dayNames), 0);
        $rawWeekdays = (clone $baseQuery)
            ->selectRaw('WEEKDAY(created_at) + 1 as dow, COUNT(*) as aggregate')
            ->whereNotNull('created_at')
            ->groupBy('dow')
            ->pluck('aggregate', 'dow');

        foreach ($rawWeekdays as $dow => $count) {
            $day = (int) $dow;
            if (isset($weekdayCounts[$day])) {
                $weekdayCounts[$day] = (int) $count;
            }
        }
        $weekday = [
            'labels' => array_values($dayNames),
            'counts' => array_values($weekdayCounts),
        ];

        // 8. Text highlights
        $textHighlights = $highlights->take(12)->map(fn (Report $r) => [
            'id' => $r->id,
            'ticket_code' => $r->ticket_code,
            'judul_laporan' => $r->judul_laporan,
            'lokasi_kejadian' => $r->lokasi_kejadian,
            'jenis_laporan' => $r->jenis_laporan,
            'category' => $r->category?->name ?? '-',
            'status_label' => $r->status_label,
            'status_badge_class' => $r->status_badge_class,
            'created_at' => $r->created_at?->format('d M Y, H:i') ?? '-',
            'url' => route('admin.reports.show', $r),
        ])->values()->all();

        return [
            'stats' => [
                'total' => $total,
                'menunggu' => $statusCounts['menunggu'],
                'diverifikasi' => $statusCounts['diverifikasi'],
                'diproses' => $statusCounts['diproses'],
                'active' => $statusCounts['diverifikasi'] + $statusCounts['diproses'],
                'selesai' => $statusCounts['selesai'],
                'ditolak' => $statusCounts['ditolak'],
                'completion_rate' => $completionRate,
                'avg_verify_hours' => $avgVerifyHours,
                'avg_resolve_hours' => $avgResolveHours,
                'with_attachment' => $withAttachment,
                'with_attachment_rate' => $total > 0 ? round(($withAttachment / $denom) * 100, 1) : 0.0,
                'total_responses' => $totalResponses,
            ],
            'jenis' => $jenisItems,
            'categories' => $categoryItems,
            'privacy' => $privacyItems,
            'status' => $statusItems,
            'jurusan' => $jurusanItems,
            'timeline' => $timeline,
            'weekday' => $weekday,
            'textHighlights' => $textHighlights,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{labels: array<int, string>, counts: array<int, int>, granularity: string}
     */
    private function buildTimelineFromDatabase(Builder $baseQuery, array $filters, mixed $minCreatedAt, mixed $maxCreatedAt): array
    {
        [$start, $end] = $this->resolveDateRange($filters);

        if (! $start || ! $end) {
            if (! $minCreatedAt || ! $maxCreatedAt) {
                $end = now()->endOfDay();
                $start = now()->subDays(13)->startOfDay();
            } else {
                $start = Carbon::parse($minCreatedAt)->startOfDay();
                $end = Carbon::parse($maxCreatedAt)->endOfDay();

                if ($start->diffInDays($end) < 6) {
                    $start = $end->copy()->subDays(6)->startOfDay();
                }
            }
        }

        $spanDays = $start->diffInDays($end);
        $useMonthly = $spanDays > 60;

        $buckets = [];
        $labels = [];

        if ($useMonthly) {
            $cursor = $start->copy()->startOfMonth();
            $endMonth = $end->copy()->startOfMonth();
            while ($cursor->lte($endMonth)) {
                $key = $cursor->format('Y-m');
                $buckets[$key] = 0;
                $labels[$key] = $cursor->translatedFormat('M Y');
                $cursor->addMonth();
            }

            $rows = (clone $baseQuery)
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as bucket, COUNT(*) as aggregate")
                ->whereNotNull('created_at')
                ->groupBy('bucket')
                ->pluck('aggregate', 'bucket');
        } else {
            $cursor = $start->copy()->startOfDay();
            $endDay = $end->copy()->startOfDay();
            while ($cursor->lte($endDay)) {
                $key = $cursor->format('Y-m-d');
                $buckets[$key] = 0;
                $labels[$key] = $cursor->format('d M');
                $cursor->addDay();
            }

            $rows = (clone $baseQuery)
                ->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as bucket, COUNT(*) as aggregate")
                ->whereNotNull('created_at')
                ->groupBy('bucket')
                ->pluck('aggregate', 'bucket');
        }

        foreach ($rows as $key => $count) {
            if (array_key_exists((string) $key, $buckets)) {
                $buckets[(string) $key] = (int) $count;
            }
        }

        return [
            'labels' => array_values($labels),
            'counts' => array_values($buckets),
            'granularity' => $useMonthly ? 'Bulanan' : 'Harian',
        ];
    }

    /**
     * @param  Collection<int, Report>  $reports
     * @return array<int, array<string, mixed>>
     */
    private function buildQuestionBreakdown(Collection $reports): array
    {
        $total = $reports->count();
        $denom = max(1, $total);

        $definitions = [
            [
                'key' => 'jenis_laporan',
                'title' => 'Jenis Laporan',
                'type' => 'Pilihan Ganda',
                'extractor' => fn (Report $r) => $r->jenis_laporan ?: 'Tidak diisi',
            ],
            [
                'key' => 'category',
                'title' => 'Kategori Bidang Tujuan',
                'type' => 'Dropdown',
                'extractor' => fn (Report $r) => $r->category?->name ?? 'Tanpa Kategori',
            ],
            [
                'key' => 'privacy',
                'title' => 'Tingkat Privasi Pelapor',
                'type' => 'Pilihan Ganda',
                'extractor' => fn (Report $r) => $r->privacy_label,
            ],
            [
                'key' => 'status',
                'title' => 'Status Penanganan',
                'type' => 'Status Alur',
                'extractor' => fn (Report $r) => $r->status_label,
            ],
            [
                'key' => 'jurusan_instansi',
                'title' => 'Jurusan / Instansi Pelapor',
                'type' => 'Jawaban Singkat',
                'extractor' => fn (Report $r) => trim((string) $r->jurusan_instansi) !== '' ? trim((string) $r->jurusan_instansi) : '(Tidak diisi)',
            ],
            [
                'key' => 'lokasi_kejadian',
                'title' => 'Lokasi Kejadian',
                'type' => 'Jawaban Singkat',
                'extractor' => fn (Report $r) => trim((string) $r->lokasi_kejadian) !== '' ? trim((string) $r->lokasi_kejadian) : '(Tidak diisi)',
            ],
            [
                'key' => 'tanggal_kejadian',
                'title' => 'Tanggal Kejadian',
                'type' => 'Tanggal',
                'extractor' => fn (Report $r) => $r->tanggal_kejadian ? $r->tanggal_kejadian->format('d M Y') : '(Tidak diisi)',
            ],
            [
                'key' => 'judul_laporan',
                'title' => 'Judul Laporan',
                'type' => 'Paragraf / Teks',
                'extractor' => fn (Report $r) => $r->judul_laporan,
            ],
        ];

        $result = [];
        foreach ($definitions as $def) {
            $grouped = $reports
                ->groupBy($def['extractor'])
                ->map(function (Collection $group, string $answer) use ($total, $denom) {
                    return [
                        'answer' => $answer,
                        'count' => $group->count(),
                        'percent' => $total > 0 ? round(($group->count() / $denom) * 100, 1) : 0.0,
                        'tickets' => $group->take(10)->map(fn (Report $r) => [
                            'ticket_code' => $r->ticket_code,
                            'judul_laporan' => $r->judul_laporan,
                            'nama_pelapor' => $r->nama_pelapor,
                            'status_label' => $r->status_label,
                            'status_badge_class' => $r->status_badge_class,
                            'created_at' => $r->created_at?->format('d M Y') ?? '-',
                            'url' => route('admin.reports.show', $r),
                        ])->values()->all(),
                        'more_count' => max(0, $group->count() - 10),
                    ];
                })
                ->sortByDesc('count')
                ->values()
                ->all();

            $answeredCount = $reports->filter(function (Report $r) use ($def) {
                $val = $def['extractor']($r);

                return $val !== '(Tidak diisi)' && $val !== 'Tidak diisi';
            })->count();

            $result[] = [
                'key' => $def['key'],
                'title' => $def['title'],
                'type' => $def['type'],
                'total_responses' => $total,
                'answered_count' => $answeredCount,
                'unique_answers' => count($grouped),
                'groups' => $grouped,
            ];
        }

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    private function formatIndividualReport(Report $r): array
    {
        return [
            'id' => $r->id,
            'ticket_code' => $r->ticket_code,
            'jenis_laporan' => $r->jenis_laporan,
            'category' => $r->category?->name ?? '-',
            'privacy_label' => $r->privacy_label,
            'status' => $r->status,
            'status_label' => $r->status_label,
            'status_badge_class' => $r->status_badge_class,
            'nama_pelapor' => $r->nama_pelapor,
            'nim_nip' => $r->nim_nip,
            'jurusan_instansi' => $r->jurusan_instansi ?: '-',
            'no_wa' => $r->no_wa,
            'judul_laporan' => $r->judul_laporan,
            'isi_laporan' => $r->isi_laporan,
            'tanggal_kejadian' => $r->tanggal_kejadian?->format('d M Y') ?? '-',
            'lokasi_kejadian' => $r->lokasi_kejadian ?: '-',
            'attachment_name' => $r->attachment_name,
            'attachment_url' => $r->attachment_path ? route('admin.reports.attachment', $r) : null,
            'created_at' => $r->created_at?->format('d M Y, H:i') ?? '-',
            'verified_at' => $r->verified_at?->format('d M Y, H:i'),
            'resolved_at' => $r->resolved_at?->format('d M Y, H:i'),
            'responses_count' => $r->responses->count(),
            'responses' => $r->responses->map(fn ($resp) => [
                'sender_name' => $resp->sender_name,
                'sender_type' => $resp->sender_type,
                'is_internal' => (bool) $resp->is_internal,
                'message' => $resp->message,
                'status_to' => $resp->status_to ? (Report::STATUSES[$resp->status_to] ?? $resp->status_to) : null,
                'created_at' => $resp->created_at?->format('d M Y, H:i') ?? '-',
            ])->values()->all(),
            'show_url' => route('admin.reports.show', $r),
            'pdf_url' => route('admin.analytics.response.pdf', $r),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    public function resolveDateRange(array $filters): array
    {
        return match ($filters['period'] ?? 'all') {
            '7d' => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
            '30d' => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
            '90d' => [now()->subDays(89)->startOfDay(), now()->endOfDay()],
            'year' => [now()->startOfYear(), now()->endOfDay()],
            'custom' => [
                ! empty($filters['date_from']) ? Carbon::parse($filters['date_from'])->startOfDay() : null,
                ! empty($filters['date_to']) ? Carbon::parse($filters['date_to'])->endOfDay() : null,
            ],
            default => [null, null],
        };
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function hasActiveFilters(array $filters): bool
    {
        return ($filters['period'] ?? 'all') !== 'all'
            || ! empty($filters['category_id'])
            || ! empty($filters['jenis'])
            || ! empty($filters['privacy'])
            || ! empty($filters['status']);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function describePeriod(array $filters): string
    {
        $period = $filters['period'] ?? 'all';
        if ($period === 'custom') {
            $from = ! empty($filters['date_from']) ? Carbon::parse($filters['date_from'])->format('d M Y') : 'Awal';
            $to = ! empty($filters['date_to']) ? Carbon::parse($filters['date_to'])->format('d M Y') : 'Sekarang';

            return "{$from} – {$to}";
        }

        return self::PERIOD_OPTIONS[$period] ?? 'Semua Waktu';
    }

    public function formatDuration(?float $hours): string
    {
        if ($hours === null) {
            return '—';
        }

        if ($hours < 24) {
            return number_format($hours, 1).' jam';
        }

        $days = round($hours / 24, 1);

        return number_format($days, 1).' hari';
    }

    public function sanitizeSpreadsheetCell(mixed $value): string
    {
        $str = (string) ($value ?? '');
        if ($str === '') {
            return '';
        }

        if (preg_match('/^[\s]*[=+\-@\t\r]/', $str) === 1) {
            return "'".$str;
        }

        return $str;
    }

    private function normalizeDate(mixed $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', trim($value))->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }
}
