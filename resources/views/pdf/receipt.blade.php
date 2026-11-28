<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tanda Terima {{ $report->ticket_code }} — SILIRA</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 18mm 18mm 16mm 18mm;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #111815;
            margin: 0;
            padding: 0;
        }
        .sheet {
            border: 1px solid #9ba8a1;
            border-radius: 4px;
            overflow: hidden;
        }
        .accent-bar {
            height: 6px;
            background-color: #1e3a8a;
            border-bottom: 2px solid #b5852a;
        }
        .header {
            padding: 20px 24px 16px 24px;
            border-bottom: 1px solid #d5dbd7;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .org-eyebrow {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #1e3a8a;
            margin: 0 0 3px 0;
        }
        .doc-title {
            font-size: 18px;
            font-weight: bold;
            color: #111815;
            margin: 0 0 2px 0;
        }
        .doc-subtitle {
            font-size: 10px;
            color: #4b5752;
            margin: 0;
        }
        .status-pill {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 2px;
            background-color: #eff6ff;
            border: 1px solid #1e3a8a;
            color: #1e3a8a;
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .timestamp {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 9.5px;
            color: #4b5752;
            margin-top: 6px;
        }
        .credentials-band {
            background-color: #f8f8f5;
            padding: 16px 24px;
            border-bottom: 1px solid #d5dbd7;
        }
        .cred-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 12px 0;
            margin-left: -12px;
            margin-right: -12px;
            width: calc(100% + 24px);
        }
        .cred-box {
            background-color: #ffffff;
            border: 1px solid #9ba8a1;
            border-radius: 4px;
            padding: 12px 14px;
            vertical-align: top;
        }
        .cred-box-pin {
            background-color: #fbf6ea;
            border: 1px solid #b5852a;
        }
        .cred-label {
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #4b5752;
            margin: 0 0 5px 0;
        }
        .cred-label-pin {
            color: #8a6116;
        }
        .cred-code {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 16px;
            font-weight: bold;
            color: #111815;
            letter-spacing: 0.5px;
            margin: 0 0 4px 0;
        }
        .cred-pin {
            font-family: 'DejaVu Sans Mono', monospace;
            font-size: 16px;
            font-weight: bold;
            color: #111815;
            letter-spacing: 3px;
            margin: 0 0 4px 0;
        }
        .cred-hint {
            font-size: 9px;
            color: #4b5752;
            margin: 0;
        }
        .cred-hint-warn {
            color: #8a6116;
            font-weight: bold;
        }
        .section {
            padding: 18px 24px;
        }
        .section-heading {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.1px;
            color: #475569;
            margin: 0 0 10px 0;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            border-top: 1px solid #e2e8f0;
        }
        .meta-table td {
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
            font-size: 10.5px;
        }
        .meta-label {
            width: 34%;
            color: #475569;
        }
        .meta-value {
            width: 66%;
            color: #0f172a;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        .meta-strong {
            font-weight: bold;
        }
        .badge-status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            background-color: #fef3c7;
            border: 1px solid #fcd34d;
            color: #92400e;
            font-size: 9.5px;
            font-weight: bold;
        }
        .notice-box {
            margin-top: 16px;
            padding: 12px 14px;
            background-color: #f8f8f5;
            border: 1px solid #d5dbd7;
            border-left: 3px solid #1e3a8a;
            border-radius: 4px;
            font-size: 9.8px;
            color: #4b5752;
            line-height: 1.55;
        }
        .notice-title {
            font-weight: bold;
            color: #111815;
            margin: 0 0 4px 0;
        }
        .mono {
            font-family: 'DejaVu Sans Mono', monospace;
            color: #111815;
        }
        .footer {
            background-color: #f8f8f5;
            border-top: 1px solid #d5dbd7;
            padding: 12px 24px;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.8px;
            color: #64748b;
        }
        .footer-right {
            text-align: right;
            font-family: 'DejaVu Sans Mono', monospace;
            color: #334155;
        }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="accent-bar"></div>

        <div class="header">
            <table class="header-table">
                <tr>
                    <td style="width: 72%;">
                        <p class="org-eyebrow">Dewan Perwakilan Mahasiswa &middot; Fakultas Dakwah &amp; Ilmu Komunikasi</p>
                        <h1 class="doc-title">Tanda Terima Laporan Resmi</h1>
                        <p class="doc-subtitle">Sistem Integrasi Layanan Interaksi dan Respon Aspirasi (SILIRA)</p>
                    </td>
                    <td style="width: 28%; text-align: right;">
                        <span class="status-pill">Tercatat</span>
                        <div class="timestamp">{{ $report->created_at->format('d M Y, H:i') }} WIB</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="credentials-band">
            <table class="cred-table">
                <tr>
                    <td class="cred-box" style="width: {{ ! empty($accessPin) ? '58%' : '100%' }};">
                        <p class="cred-label">Nomor / Kode Tiket</p>
                        <p class="cred-code">{{ $report->ticket_code }}</p>
                        <p class="cred-hint">Gunakan kode ini untuk memantau progres di halaman /lacak</p>
                    </td>
                    @if (! empty($accessPin))
                        <td class="cred-box cred-box-pin" style="width: 42%;">
                            <p class="cred-label cred-label-pin">PIN Akses Pribadi</p>
                            <p class="cred-pin">{{ $accessPin }}</p>
                            <p class="cred-hint cred-hint-warn">Rahasia &middot; untuk verifikasi balasan &amp; laporan Rahasia</p>
                        </td>
                    @endif
                </tr>
            </table>
        </div>

        <div class="section">
            <p class="section-heading">Ringkasan Berkas Laporan</p>

            <table class="meta-table">
                <tr>
                    <td class="meta-label">Judul laporan</td>
                    <td class="meta-value meta-strong">{{ $report->judul_laporan }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Klasifikasi laporan</td>
                    <td class="meta-value">{{ $report->jenis_laporan }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Kategori bidang tujuan</td>
                    <td class="meta-value">{{ $report->category?->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Tingkat privasi</td>
                    <td class="meta-value meta-strong">{{ $report->privacy_label }}</td>
                </tr>
                <tr>
                    <td class="meta-label">Identitas tampilan publik</td>
                    <td class="meta-value">{{ $report->public_reporter_name }}</td>
                </tr>
                @if ($report->tanggal_kejadian)
                    <tr>
                        <td class="meta-label">Tanggal kejadian</td>
                        <td class="meta-value">{{ $report->tanggal_kejadian->format('d M Y') }}</td>
                    </tr>
                @endif
                @if ($report->lokasi_kejadian)
                    <tr>
                        <td class="meta-label">Lokasi kejadian</td>
                        <td class="meta-value">{{ $report->lokasi_kejadian }}</td>
                    </tr>
                @endif
                <tr>
                    <td class="meta-label">Waktu penerimaan sistem</td>
                    <td class="meta-value mono">{{ $report->created_at->format('d M Y, H:i') }} WIB</td>
                </tr>
                <tr>
                    <td class="meta-label">Status saat ini</td>
                    <td class="meta-value">
                        <span class="badge-status">{{ $report->status_label }}</span>
                    </td>
                </tr>
            </table>

            <div class="notice-box">
                <p class="notice-title">Panduan Tindak Lanjut &amp; Keamanan</p>
                Simpan berkas PDF ini sebagai tanda terima resmi pelaporan Anda. Verifikasi awal dilakukan oleh Komisi Aspirasi DPM FDIK dalam 1&ndash;2 hari kerja. Untuk memantau status atau mengirim tanggapan/bukti tambahan, buka <span class="mono">{{ $trackingUrl }}</span> dan masukkan Kode Tiket beserta PIN Akses Pribadi di atas. Jangan membagikan PIN kepada pihak lain.
            </div>
        </div>

        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td style="width: 68%;">
                        Dokumen ini diterbitkan secara elektronik oleh SILIRA &middot; Sah tanpa tanda tangan basah.<br>
                        Diunduh pada {{ $printedAt }} WIB
                    </td>
                    <td class="footer-right" style="width: 32%;">
                        REF/{{ $report->ticket_code }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
