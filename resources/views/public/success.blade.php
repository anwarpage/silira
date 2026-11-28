@extends('layouts.public')

@section('title', 'Tanda Terima Tiket ' . $report->ticket_code . ' — SILIRA')

@section('content')
    <div class="max-w-2xl mx-auto px-5 sm:px-8 py-10 sm:py-14 print:max-w-none print:px-0 print:py-0">
        {{-- Top action bar (hidden on print) --}}
        <div class="no-print mb-5 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2 text-[14px] font-medium text-[#14532d]">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-[6px] bg-[#dcfce7] border border-[#16a34a]/35 text-[12px]">&#10003;</span>
                Laporan berhasil tercatat di sistem SILIRA
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="copyBtn" class="s-btn-secondary h-[38px] px-3.5 text-[13px]">
                    Salin kode &amp; PIN
                </button>
                <a href="{{ route('reports.receipt', $report->ticket_code) }}" class="s-btn-primary h-[38px] px-4 text-[13px]">
                    Unduh bukti (PDF)
                </a>
            </div>
        </div>

        {{-- Official Institutional Receipt Sheet --}}
        <div class="receipt-sheet bg-white rounded-[6px] border border-[#d0d7de] overflow-hidden print:rounded-none print:border-[#0f172a]/30">
            {{-- Official Double Accent Bar (Emerald + Gold) --}}
            <div class="h-1.5 bg-[#1e3a8a]"></div>
            <div class="h-0.5 bg-[#b5852a]"></div>

            {{-- Institutional Header --}}
            <div class="px-7 sm:px-9 py-6 border-b border-[#e5e7eb] flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div class="flex items-start gap-3.5">
                    <x-brand-logo :markOnly="true" size="lg" />
                    <div>
                        <p class="text-[12px] font-medium text-[#1e3a8a]">
                            Dewan Perwakilan Mahasiswa &middot; Fakultas Dakwah dan Ilmu Komunikasi
                        </p>
                        <h1 class="mt-0.5 text-[22px] sm:text-[24px] font-semibold text-[#0f172a]">
                            Tanda Terima Laporan Resmi
                        </h1>
                        <p class="text-[13px] text-[#475569]">
                            Sistem Integrasi Layanan Interaksi dan Respon Aspirasi (SILIRA)
                        </p>
                    </div>
                </div>

                <div class="sm:text-right shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-[6px] text-[12px] font-medium bg-[#eff6ff] text-[#1e3a8a] border border-[#1e3a8a]/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#1e3a8a]"></span>
                        Tercatat
                    </span>
                    <p class="mt-1.5 font-mono text-[12px] text-[#475569]">
                        {{ $report->created_at->format('d M Y, H:i') }} WIB
                    </p>
                </div>
            </div>

            {{-- Credentials Box (Ticket Code + Access PIN) --}}
            <div class="px-7 sm:px-9 py-6 bg-[#f8fafc] border-b border-[#e5e7eb]">
                <div class="grid grid-cols-1 {{ ! empty($accessPin) ? 'sm:grid-cols-12' : '' }} gap-4">
                    <div class="{{ ! empty($accessPin) ? 'sm:col-span-7' : '' }} rounded-[9px] bg-white border border-[#d0d7de] px-5 py-4">
                        <p class="text-[12px] font-medium text-[#475569]">Nomor / Kode Tiket</p>
                        <p id="ticketCodeText" class="mt-1 text-[19px] sm:text-[21px] font-semibold tracking-[0.03em] text-[#0f172a] font-mono whitespace-nowrap">
                            {{ $report->ticket_code }}
                        </p>
                        <p class="mt-1 text-[12.5px] text-[#475569]">Gunakan untuk melacak status di halaman <span class="font-mono">/lacak</span></p>
                    </div>

                    @if (! empty($accessPin))
                        <div class="sm:col-span-5 rounded-[9px] bg-[#fbf6ea] border border-[#b5852a]/55 px-5 py-4">
                            <p class="text-[12px] font-medium text-[#8a6116]">PIN Akses Pribadi</p>
                            <p id="accessPinText" class="mt-1 text-[19px] sm:text-[21px] font-semibold tracking-[0.18em] text-[#0f172a] font-mono whitespace-nowrap">
                                {{ $accessPin }}
                            </p>
                            <p class="mt-1 text-[12px] text-[#8a6116] font-medium">Rahasia &middot; tampil sekali untuk verifikasi balasan / laporan Rahasia</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Structured Report Metadata --}}
            <div class="px-7 sm:px-9 py-6">
                <p class="text-[12.5px] font-semibold text-[#475569] mb-3">
                    Ringkasan Berkas Laporan
                </p>

                <dl class="divide-y divide-[#e5e7eb] text-[14px] border-t border-b border-[#e5e7eb]">
                    <div class="py-3 grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4">
                        <dt class="sm:col-span-4 text-[#475569]">Judul laporan</dt>
                        <dd class="sm:col-span-8 font-medium text-[#0f172a] break-words [overflow-wrap:anywhere]">{{ $report->judul_laporan }}</dd>
                    </div>
                    <div class="py-3 grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4">
                        <dt class="sm:col-span-4 text-[#475569]">Klasifikasi</dt>
                        <dd class="sm:col-span-8 text-[#0f172a]">{{ $report->jenis_laporan }}</dd>
                    </div>
                    <div class="py-3 grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4">
                        <dt class="sm:col-span-4 text-[#475569]">Kategori bidang</dt>
                        <dd class="sm:col-span-8 text-[#0f172a]">{{ $report->category?->name ?? '-' }}</dd>
                    </div>
                    <div class="py-3 grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4">
                        <dt class="sm:col-span-4 text-[#475569]">Tingkat privasi</dt>
                        <dd class="sm:col-span-8 text-[#0f172a] font-medium">{{ $report->privacy_label }}</dd>
                    </div>
                    <div class="py-3 grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4">
                        <dt class="sm:col-span-4 text-[#475569]">Identitas tampilan</dt>
                        <dd class="sm:col-span-8 text-[#0f172a]">{{ $report->public_reporter_name }}</dd>
                    </div>
                    <div class="py-3 grid grid-cols-1 sm:grid-cols-12 gap-1 sm:gap-4">
                        <dt class="sm:col-span-4 text-[#475569]">Status saat ini</dt>
                        <dd class="sm:col-span-8">
                            <span class="inline-block px-2.5 py-0.5 rounded-[6px] text-[13px] font-medium {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
                        </dd>
                    </div>
                </dl>

                {{-- Verification & SLA Notice --}}
                <div class="mt-5 rounded-[9px] bg-[#f8fafc] border border-[#e5e7eb] px-4 py-3.5 text-[13px] text-[#475569] leading-relaxed">
                    <strong class="text-[#0f172a] font-medium">Panduan tindak lanjut:</strong>
                    Simpan dokumen ini sebagai bukti pelaporan resmi. Verifikasi awal dilakukan oleh Komisi Aspirasi DPM FDIK dalam 1&ndash;2 hari kerja. Untuk memantau progres atau mengirim lampiran tambahan, kunjungi
                    <span class="font-mono text-[#0f172a]">{{ route('tracking.index') }}</span>
                    dengan memasukkan Nomor Tiket dan PIN Akses di atas.
                </div>
            </div>

            {{-- Official Document Footer --}}
            <div class="px-7 sm:px-9 py-4 bg-[#f8fafc] border-t border-[#e5e7eb] flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[12px] text-[#475569]">
                <span>Dokumen diterbitkan secara elektronik oleh SILIRA &middot; Sah tanpa tanda tangan basah.</span>
                <span class="font-mono shrink-0">REF/{{ $report->ticket_code }}</span>
            </div>
        </div>

        {{-- Bottom Navigation Actions (hidden on print) --}}
        <div class="mt-6 flex flex-col sm:flex-row gap-3 no-print">
            <a href="{{ route('tracking.show', $report->ticket_code) }}" class="s-btn-primary flex-1">
                Pantau Tiket Ini Sekarang &rarr;
            </a>
            <a href="{{ route('public.feed') }}" class="s-btn-secondary">
                Direktori Aspirasi
            </a>
            <a href="{{ route('home') }}" class="s-btn-secondary">
                Beranda
            </a>
        </div>
    </div>

    <script>
        document.getElementById('copyBtn')?.addEventListener('click', async () => {
            const code = document.getElementById('ticketCodeText').textContent.trim();
            const pinEl = document.getElementById('accessPinText');
            const payload = pinEl ? `Kode Tiket: ${code}\nPIN Akses: ${pinEl.textContent.trim()}` : code;
            const btn = document.getElementById('copyBtn');
            try {
                await navigator.clipboard.writeText(payload);
                btn.textContent = 'Tersalin ✓';
                setTimeout(() => { btn.textContent = 'Salin kode & PIN'; }, 1800);
            } catch {
                alert(payload);
            }
        });
    </script>
@endsection
