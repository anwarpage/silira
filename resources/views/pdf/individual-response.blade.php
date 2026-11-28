<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Respons {{ $report->ticket_code }} — SILIRA</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 16mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10.5px;
            line-height: 1.5;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
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
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }
        .mono {
            font-family: 'DejaVu Sans Mono', monospace;
        }
        .badge {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 3px;
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e3a8a;
            font-size: 9px;
            font-weight: bold;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            border-top: 1px solid #cbd5e1;
        }
        .meta-table td {
            padding: 7px 6px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }
        .meta-label {
            width: 30%;
            color: #475569;
            font-weight: bold;
            font-size: 9.5px;
        }
        .meta-value {
            width: 70%;
            color: #0f172a;
        }
        .box {
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            padding: 12px 14px;
            background-color: #f8fafc;
            margin-bottom: 14px;
        }
        .section-heading {
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: #1e3a8a;
            margin: 0 0 8px 0;
        }
        .timeline-item {
            border-left: 2px solid #cbd5e1;
            padding: 0 0 10px 12px;
            margin-bottom: 6px;
        }
        .timeline-meta {
            font-size: 8.8px;
            color: #475569;
            margin-bottom: 3px;
        }
        .footer {
            margin-top: 16px;
            padding-top: 8px;
            border-top: 1px solid #cbd5e1;
            font-size: 8.5px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 70%; vertical-align: top;">
                    <p class="eyebrow">Lembar Respons Individual &middot; SILIRA DPM FDIK</p>
                    <h1 class="title">{{ $report->judul_laporan }}</h1>
                </td>
                <td style="width: 30%; text-align: right; vertical-align: top;">
                    <span class="badge">{{ $report->status_label }}</span>
                    <div class="mono" style="font-size: 9.5px; margin-top: 5px;">{{ $report->ticket_code }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Waktu Masuk</td>
            <td class="meta-value mono">{{ $report->created_at?->format('d M Y, H:i') ?? '-' }} WIB</td>
        </tr>
        <tr>
            <td class="meta-label">Jenis Laporan</td>
            <td class="meta-value">{{ $report->jenis_laporan }}</td>
        </tr>
        <tr>
            <td class="meta-label">Kategori Bidang</td>
            <td class="meta-value">{{ $report->category?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tingkat Privasi</td>
            <td class="meta-value">{{ $report->privacy_label }}</td>
        </tr>
        <tr>
            <td class="meta-label">Nama Pelapor</td>
            <td class="meta-value">{{ $report->nama_pelapor }} (<span class="mono">{{ $report->nim_nip }}</span>)</td>
        </tr>
        <tr>
            <td class="meta-label">Jurusan / Instansi</td>
            <td class="meta-value">{{ $report->jurusan_instansi ?: '-' }}</td>
        </tr>
        <tr>
            <td class="meta-label">No. WhatsApp</td>
            <td class="meta-value mono">{{ $report->no_wa }}</td>
        </tr>
        <tr>
            <td class="meta-label">Tanggal &amp; Lokasi Kejadian</td>
            <td class="meta-value">
                {{ $report->tanggal_kejadian?->format('d M Y') ?? '-' }}
                @if ($report->lokasi_kejadian)
                    &middot; {{ $report->lokasi_kejadian }}
                @endif
            </td>
        </tr>
        @if ($report->attachment_name)
            <tr>
                <td class="meta-label">Lampiran</td>
                <td class="meta-value">{{ $report->attachment_name }}</td>
            </tr>
        @endif
    </table>

    <div class="box">
        <p class="section-heading">Isi Laporan / Aspirasi</p>
        <div style="white-space: pre-line;">{{ $report->isi_laporan }}</div>
    </div>

    @if ($report->responses->isNotEmpty())
        <p class="section-heading">Riwayat Tindak Lanjut ({{ $report->responses->count() }})</p>
        @foreach ($report->responses as $resp)
            <div class="timeline-item">
                <div class="timeline-meta">
                    <strong>{{ $resp->sender_name }}</strong>
                    @if ($resp->is_internal)
                        &middot; <em>Catatan Internal</em>
                    @endif
                    @if ($resp->status_to)
                        &middot; Status &rarr; {{ \App\Models\Report::STATUSES[$resp->status_to] ?? $resp->status_to }}
                    @endif
                    &middot; <span class="mono">{{ $resp->created_at?->format('d M Y, H:i') }} WIB</span>
                </div>
                <div style="white-space: pre-line;">{{ $resp->message }}</div>
            </div>
        @endforeach
    @endif

    <div class="footer">
        Dicetak dari Panel Admin SILIRA pada {{ $printedAt }} WIB &middot; Dokumen Internal DPM FDIK
    </div>
</body>
</html>
