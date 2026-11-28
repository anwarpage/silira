<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Analitik SILIRA — DPM FDIK</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 16mm 16mm 15mm 16mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            line-height: 1.48;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .eyebrow {
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #1e3a8a;
            margin: 0 0 3px 0;
        }
        .title {
            font-size: 17px;
            font-weight: bold;
            color: #0f172a;
            margin: 0 0 2px 0;
        }
        .subtitle {
            font-size: 9.5px;
            color: #475569;
            margin: 0;
        }
        .meta-box {
            text-align: right;
            font-size: 9px;
            color: #475569;
        }
        .meta-pill {
            display: inline-block;
            padding: 3px 8px;
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e3a8a;
            font-weight: bold;
            border-radius: 3px;
            margin-bottom: 4px;
        }
        .kpi-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin: 0 -8px 14px -8px;
            width: calc(100% + 16px);
        }
        .kpi-card {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 10px 12px;
            width: 25%;
            vertical-align: top;
        }
        .kpi-label {
            font-size: 8.5px;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 4px 0;
        }
        .kpi-value {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
        }
        .kpi-sub {
            font-size: 8.5px;
            color: #475569;
            margin: 3px 0 0 0;
        }
        .sla-strip {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            border: 1px solid #e2e8f0;
        }
        .sla-strip td {
            padding: 8px 12px;
            border-right: 1px solid #e2e8f0;
            background-color: #ffffff;
            width: 33.33%;
        }
        .sla-strip td:last-child {
            border-right: none;
        }
        .section-title {
            font-size: 10.5px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin: 14px 0 8px 0;
            padding-bottom: 4px;
            border-bottom: 1px solid #cbd5e1;
        }
        .two-col {
            width: 100%;
            border-collapse: separate;
            border-spacing: 12px 0;
            margin: 0 -12px;
            width: calc(100% + 24px);
        }
        .two-col > tbody > tr > td {
            width: 50%;
            vertical-align: top;
        }
        .chart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .chart-table td {
            padding: 4px 0;
            vertical-align: middle;
        }
        .bar-track {
            width: 100%;
            height: 7px;
            background-color: #e2e8f0;
            border-radius: 3px;
            overflow: hidden;
        }
        .bar-fill {
            height: 7px;
            background-color: #1e3a8a;
            border-radius: 3px;
        }
        .mono {
            font-family: 'DejaVu Sans Mono', monospace;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 8.8px;
        }
        .data-table th {
            background-color: #0f172a;
            color: #ffffff;
            text-align: left;
            padding: 6px 7px;
            font-weight: bold;
        }
        .data-table td {
            padding: 6px 7px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .footer {
            margin-top: 18px;
            padding-top: 8px;
            border-top: 1px solid #cbd5e1;
            font-size: 8.5px;
            color: #64748b;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 66%;">
                    <p class="eyebrow">Dewan Perwakilan Mahasiswa &middot; Fakultas Dakwah &amp; Ilmu Komunikasi</p>
                    <h1 class="title">Laporan Analitik &amp; Ringkasan Respons</h1>
                    <p class="subtitle">Sistem Integrasi Layanan Interaksi dan Respon Aspirasi (SILIRA)</p>
                </td>
                <td class="meta-box" style="width: 34%;">
                    <div class="meta-pill">{{ $periodLabel }}</div>
                    <div>Dicetak: {{ $generatedAt }} WIB</div>
                    <div>Oleh: {{ $generatedBy }}</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Primary KPI Strip --}}
    <table class="kpi-table">
        <tr>
            <td class="kpi-card">
                <p class="kpi-label">Total Respons</p>
                <p class="kpi-value">{{ number_format($summary['stats']['total']) }}</p>
                <p class="kpi-sub">{{ number_format($summary['stats']['total_responses']) }} total interaksi</p>
            </td>
            <td class="kpi-card">
                <p class="kpi-label">Menunggu</p>
                <p class="kpi-value" style="color: #92400e;">{{ number_format($summary['stats']['menunggu']) }}</p>
                <p class="kpi-sub">Antrean verifikasi</p>
            </td>
            <td class="kpi-card">
                <p class="kpi-label">Diproses</p>
                <p class="kpi-value" style="color: #1e3a8a;">{{ number_format($summary['stats']['active']) }}</p>
                <p class="kpi-sub">Diverifikasi &amp; tindak lanjut</p>
            </td>
            <td class="kpi-card">
                <p class="kpi-label">Tuntas</p>
                <p class="kpi-value" style="color: #15803d;">{{ number_format($summary['stats']['selesai']) }}</p>
                <p class="kpi-sub">Rasio selesai {{ $summary['stats']['completion_rate'] }}%</p>
            </td>
        </tr>
    </table>

    {{-- SLA & Attachment Metrics --}}
    <table class="sla-strip">
        <tr>
            <td>
                <div class="kpi-label">Rata-rata Verifikasi</div>
                <strong class="mono" style="font-size: 11.5px;">{{ $avgVerifyLabel }}</strong>
            </td>
            <td>
                <div class="kpi-label">Rata-rata Penyelesaian</div>
                <strong class="mono" style="font-size: 11.5px;">{{ $avgResolveLabel }}</strong>
            </td>
            <td>
                <div class="kpi-label">Respons Berlampiran</div>
                <strong class="mono" style="font-size: 11.5px;">{{ $summary['stats']['with_attachment'] }} ({{ $summary['stats']['with_attachment_rate'] }}%)</strong>
            </td>
        </tr>
    </table>

    {{-- Distribution Pair 1: Jenis & Privasi --}}
    <table class="two-col">
        <tr>
            <td>
                <div class="section-title">Distribusi Jenis Laporan</div>
                <table class="chart-table">
                    @foreach ($summary['jenis'] as $item)
                        <tr>
                            <td style="width: 62%;">{{ $item['label'] }}</td>
                            <td style="width: 38%; text-align: right;" class="mono">{{ $item['count'] }} ({{ $item['percent'] }}%)</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-top: 0; padding-bottom: 6px;">
                                <div class="bar-track">
                                    <div class="bar-fill" style="width: {{ min(100, max(0, $item['percent'])) }}%; background-color: {{ $item['color'] }};"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </td>
            <td>
                <div class="section-title">Distribusi Tingkat Privasi</div>
                <table class="chart-table">
                    @foreach ($summary['privacy'] as $item)
                        <tr>
                            <td style="width: 62%;">{{ $item['label'] }}</td>
                            <td style="width: 38%; text-align: right;" class="mono">{{ $item['count'] }} ({{ $item['percent'] }}%)</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-top: 0; padding-bottom: 6px;">
                                <div class="bar-track">
                                    <div class="bar-fill" style="width: {{ min(100, max(0, $item['percent'])) }}%; background-color: {{ $item['color'] }};"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    {{-- Distribution Pair 2: Status & Kategori --}}
    <table class="two-col">
        <tr>
            <td>
                <div class="section-title">Komposisi Status Penanganan</div>
                <table class="chart-table">
                    @foreach ($summary['status'] as $item)
                        <tr>
                            <td style="width: 62%;">{{ $item['label'] }}</td>
                            <td style="width: 38%; text-align: right;" class="mono">{{ $item['count'] }} ({{ $item['percent'] }}%)</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-top: 0; padding-bottom: 6px;">
                                <div class="bar-track">
                                    <div class="bar-fill" style="width: {{ min(100, max(0, $item['percent'])) }}%; background-color: {{ $item['color'] }};"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </td>
            <td>
                <div class="section-title">Distribusi Kategori Bidang</div>
                <table class="chart-table">
                    @foreach ($summary['categories'] as $item)
                        <tr>
                            <td style="width: 66%;">{{ $item['label'] }}</td>
                            <td style="width: 34%; text-align: right;" class="mono">{{ $item['count'] }} ({{ $item['percent'] }}%)</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-top: 0; padding-bottom: 5px;">
                                <div class="bar-track">
                                    <div class="bar-fill" style="width: {{ min(100, max(0, $item['percent'])) }}%;"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </td>
        </tr>
    </table>

    {{-- Jurusan Breakdown --}}
    @if (count($summary['jurusan']) > 0)
        <div class="section-title">Sebaran Jurusan / Instansi Pelapor (Teratas)</div>
        <table class="chart-table">
            @foreach ($summary['jurusan'] as $item)
                <tr>
                    <td style="width: 48%;">{{ $item['label'] }}</td>
                    <td style="width: 34%; padding-right: 10px;">
                        <div class="bar-track">
                            <div class="bar-fill" style="width: {{ min(100, max(0, $item['percent'])) }}%; background-color: #0284c7;"></div>
                        </div>
                    </td>
                    <td style="width: 18%; text-align: right;" class="mono">{{ $item['count'] }} ({{ $item['percent'] }}%)</td>
                </tr>
            @endforeach
        </table>
    @endif

    {{-- Recent Responses Table --}}
    @if ($reports->isNotEmpty())
        <div class="section-title" style="margin-top: 16px;">Daftar Respons Terbaru ({{ $reports->count() }} dari {{ $summary['stats']['total'] }})</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 16%;">Tiket</th>
                    <th style="width: 14%;">Tanggal</th>
                    <th style="width: 36%;">Judul &amp; Kategori</th>
                    <th style="width: 18%;">Pelapor</th>
                    <th style="width: 16%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reports as $item)
                    <tr>
                        <td class="mono">{{ $item->ticket_code }}</td>
                        <td class="mono">{{ $item->created_at?->format('d/m/Y') ?? '-' }}</td>
                        <td>
                            <strong>{{ $item->judul_laporan }}</strong><br>
                            <span style="color: #475569;">{{ $item->jenis_laporan }} &middot; {{ $item->category?->name ?? '-' }}</span>
                        </td>
                        <td>
                            {{ $item->nama_pelapor }}<br>
                            <span class="mono" style="color: #475569;">{{ $item->nim_nip }}</span>
                        </td>
                        <td><strong>{{ $item->status_label }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td>Dokumen analitik otomatis SILIRA &middot; DPM Fakultas Dakwah &amp; Ilmu Komunikasi</td>
                <td style="text-align: right;" class="mono">{{ $generatedAt }} WIB</td>
            </tr>
        </table>
    </div>
</body>
</html>
