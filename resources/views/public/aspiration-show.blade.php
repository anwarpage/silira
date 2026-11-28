@extends('layouts.public')

@section('title', $report->judul_laporan . ' — Aspirasi Publik SILIRA')

@section('content')
    @php
        $steps = ['menunggu' => 'Menunggu', 'diverifikasi' => 'Diverifikasi', 'diproses' => 'Diproses', 'selesai' => 'Selesai'];
        $order = ['menunggu' => 1, 'diverifikasi' => 2, 'diproses' => 3, 'selesai' => 4, 'ditolak' => 0];
        $currentStep = $order[$report->status] ?? 1;
    @endphp

    <div class="max-w-3xl mx-auto px-5 sm:px-8 py-10">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
            <a href="{{ route('public.feed') }}" class="inline-flex items-center gap-1.5 text-[14px] font-medium text-[#475569] hover:text-[#1e3a8a] transition">
                &larr; Kembali ke direktori aspirasi
            </a>
            <span class="text-[13px] text-[#475569]">Tampilan transparansi publik (hanya baca)</span>
        </div>

        <article class="s-card-accent p-6 sm:p-8">
            <div class="flex flex-wrap items-center gap-2.5 text-[13px]">
                <span class="font-mono font-medium px-2.5 py-0.5 rounded-[6px] bg-[#0f172a] text-[#ffffff]">{{ $report->ticket_code }}</span>
                <span class="px-2.5 py-0.5 rounded-[6px] font-medium {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
                <span class="text-[#475569]">{{ $report->jenis_laporan }}</span>
                <span class="text-[#475569]">&middot; {{ $report->privacy_label }}</span>
            </div>

            <h1 class="mt-4 text-[24px] sm:text-[30px] font-semibold leading-snug text-[#0f172a] break-words [overflow-wrap:anywhere]">
                {{ $report->judul_laporan }}
            </h1>
            <p class="mt-2 text-[14px] text-[#475569]">
                {{ $report->public_reporter_name }}
                @if ($report->category) &middot; {{ $report->category->name }} @endif
                &middot; {{ $report->created_at->format('d M Y, H:i') }} WIB
            </p>

            @if ($report->status === 'ditolak')
                <div class="mt-6 rounded-[9px] bg-[#fee2e2] border border-[#b91c1c]/30 p-4 text-[14px] text-[#991b1b]">
                    Laporan ini berstatus Ditolak. Lihat penjelasan resmi dari Admin DPM pada riwayat tindak lanjut di bawah.
                </div>
            @else
                <div class="mt-6 grid grid-cols-4 gap-2.5">
                    @foreach ($steps as $key => $label)
                        @php $done = $currentStep >= $order[$key]; @endphp
                        <div>
                            <div class="h-1.5 rounded-[6px] {{ $done ? 'bg-[#1e3a8a]' : 'bg-[#e5e7eb]' }}"></div>
                            <span class="block mt-2 text-[12px] sm:text-[13px] {{ $done ? 'font-semibold text-[#0f172a]' : 'text-[#475569]' }}">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-6 pt-6 border-t border-[#e5e7eb]">
                <p class="text-[16px] text-[#0f172a] leading-relaxed whitespace-pre-line break-words [overflow-wrap:anywhere]">{{ $report->isi_laporan }}</p>

                @if ($report->tanggal_kejadian || $report->lokasi_kejadian)
                    <div class="mt-5 flex flex-wrap gap-x-6 gap-y-2 text-[14px] text-[#475569] bg-[#f8fafc] border border-[#e5e7eb] rounded-[9px] px-4 py-3">
                        @if ($report->tanggal_kejadian)
                            <span><strong class="text-[#0f172a] font-medium">Tanggal:</strong> {{ $report->tanggal_kejadian->format('d M Y') }}</span>
                        @endif
                        @if ($report->lokasi_kejadian)
                            <span class="break-words [overflow-wrap:anywhere]"><strong class="text-[#0f172a] font-medium">Lokasi:</strong> {{ $report->lokasi_kejadian }}</span>
                        @endif
                    </div>
                @endif

                @if ($report->attachment_path)
                    <div class="mt-6">
                        <p class="text-[13px] font-medium text-[#475569] mb-2">Lampiran pendukung</p>
                        @if ($report->isAttachmentImage())
                            <div class="inline-block rounded-[9px] border border-[#e5e7eb] bg-[#f8fafc] p-2.5 max-w-full">
                                <a href="{{ route('tracking.attachment', $report->ticket_code) }}" target="_blank" rel="noopener noreferrer" class="block">
                                    <img src="{{ route('tracking.attachment', $report->ticket_code) }}" alt="Lampiran Bukti" class="max-h-72 max-w-full rounded-[6px] object-contain">
                                </a>
                                <div class="mt-2 flex items-center justify-between gap-3 px-1 text-[12px] text-[#475569]">
                                    <span class="truncate">{{ $report->attachment_name ?? 'Lampiran gambar' }}</span>
                                    <a href="{{ route('tracking.attachment', $report->ticket_code) }}" target="_blank" rel="noopener noreferrer" class="s-link shrink-0">Buka penuh &nearr;</a>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('tracking.attachment', $report->ticket_code) }}" target="_blank" rel="noopener noreferrer" class="s-btn-secondary h-[40px] text-[14px]">
                                Unduh lampiran ({{ $report->attachment_name ?? 'Berkas' }})
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </article>

        {{-- Public Timeline (Read-Only) --}}
        <section class="mt-6 s-card p-6 sm:p-8">
            <div class="flex items-center justify-between gap-3">
                <h2 class="font-sans text-[18px] font-semibold text-[#0f172a]">Riwayat tindak lanjut resmi</h2>
                <span class="font-mono text-[12px] px-2 py-0.5 rounded-[6px] bg-[#f8fafc] border border-[#e5e7eb] text-[#475569]">
                    {{ $report->publicResponses->count() }} entri
                </span>
            </div>

            <div class="mt-5 divide-y divide-[#e5e7eb]">
                @forelse ($report->publicResponses as $res)
                    <div class="py-4 first:pt-0">
                        <div class="flex flex-wrap items-center justify-between gap-2 text-[13px]">
                            <div class="flex flex-wrap items-center gap-2 min-w-0">
                                <span class="font-semibold text-[#0f172a]">{{ $res->sender_type === 'reporter' ? $report->public_reporter_name : $res->sender_name }}</span>
                                <span class="px-2 py-0.5 rounded-[6px] bg-[#f8fafc] text-[#475569]">
                                    {{ $res->sender_type === 'admin' ? 'Admin DPM' : ($res->sender_type === 'reporter' ? 'Pelapor' : 'Sistem') }}
                                </span>
                                @if ($res->status_to)
                                    <span class="font-medium text-[#1e3a8a]">&rarr; {{ \App\Models\Report::STATUSES[$res->status_to] ?? $res->status_to }}</span>
                                @endif
                            </div>
                            <time class="font-mono text-[#475569] shrink-0">{{ $res->created_at->format('d M Y, H:i') }}</time>
                        </div>
                        <p class="mt-2 text-[15px] text-[#0f172a] leading-relaxed whitespace-pre-line break-words [overflow-wrap:anywhere]">{{ $res->message }}</p>
                        @if ($res->attachment_path)
                            <a href="{{ route('tracking.response.attachment', [$report->ticket_code, $res]) }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-block text-[14px] s-link break-all">
                                Lampiran: {{ $res->attachment_name ?? 'Berkas' }}
                            </a>
                        @endif
                    </div>
                @empty
                    <p class="text-[15px] text-[#475569]">Belum ada riwayat tindak lanjut.</p>
                @endforelse
            </div>

            <div class="mt-6 pt-5 border-t border-[#e5e7eb] flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-[#f8fafc] -mx-6 sm:-mx-8 -mb-6 sm:-mb-8 px-6 sm:px-8 py-4 rounded-b-[6px]">
                <p class="text-[14px] text-[#475569]">
                    Anda pelapor tiket ini dan ingin mengirim klarifikasi atau bukti tambahan?
                </p>
                <a href="{{ route('tracking.show', $report->ticket_code) }}" class="s-btn-secondary h-[38px] px-4 text-[13px] shrink-0">
                    Buka di halaman Lacak &rarr;
                </a>
            </div>
        </section>
    </div>
@endsection
