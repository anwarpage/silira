@extends('layouts.public')

@section('title', 'Silira — Portal Aspirasi DPM FDIK')

@section('content')
    {{-- Section 1: Polished Civic-SaaS Hero with Smooth Bespoke Illustration --}}
    <section class="bg-white py-12 lg:py-20">
        <div class="max-w-[1180px] mx-auto px-5 sm:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                <div class="lg:col-span-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#eff6ff] border border-[#1e3a8a]/12 text-[13px] font-medium text-[#1e3a8a]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#2563eb]" aria-hidden="true"></span>
                        <span>DPM FDIK &middot; {{ number_format($stats['total']) }} laporan tercatat</span>
                    </div>

                    <h1 class="mt-5 text-[40px] sm:text-[54px] font-semibold leading-[1.06] tracking-[-0.04em] text-[#0f172a]">
                        Suara mahasiswa, terlacak jelas.
                    </h1>

                    <p class="mt-4 text-[17px] leading-[1.55] text-[#475569] max-w-md">
                        Kirim aspirasi tanpa login. Pantau progres tindak lanjut DPM langsung lewat kode tiket.
                    </p>

                    <div class="mt-7 flex flex-wrap items-center gap-3">
                        <a href="{{ route('reports.create') }}" class="s-btn-primary h-[46px] px-6 text-[15px]">
                            Kirim Aspirasi
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </a>
                        <a href="{{ route('public.feed') }}" class="s-btn-secondary h-[46px] px-5 text-[15px]">
                            Lihat Aspirasi
                        </a>
                    </div>

                    {{-- Unified Sleek Quick Track Bar --}}
                    <form action="{{ route('tracking.lookup') }}" method="POST" class="mt-8 pt-6 border-t border-[#0f172a]/8 max-w-md">
                        @csrf
                        <label for="quick_ticket" class="block text-[13px] font-medium text-[#475569] mb-2">
                            Sudah punya kode tiket?
                        </label>
                        <div class="flex items-center p-1.5 rounded-[12px] bg-[#f8fafc] border border-[#0f172a]/12 focus-within:border-[#1e3a8a] focus-within:bg-white focus-within:ring-[3px] focus-within:ring-[#1e3a8a]/12 transition">
                            <input
                                type="text"
                                name="ticket_code"
                                id="quick_ticket"
                                placeholder="SLR-2026-FDIK01"
                                class="flex-1 min-w-0 bg-transparent px-3 py-2 text-[14.5px] font-mono uppercase text-[#0f172a] placeholder:normal-case placeholder:font-sans placeholder:text-[#94a3b8] focus:outline-none"
                                required
                            >
                            <button type="submit" class="inline-flex items-center justify-center h-[38px] px-4 rounded-[8px] bg-[#0f172a] text-white text-[13.5px] font-medium hover:bg-[#1e3a8a] transition shrink-0 cursor-pointer">
                                Lacak
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Smooth Hero Illustration + Polished Live Metrics --}}
                <div class="lg:col-span-6">
                    <div class="rounded-[18px] overflow-hidden bg-white">
                        <img
                            src="{{ asset('images/hero-illustration.jpg') }}"
                            alt="Ilustrasi alur penyampaian dan pelacakan aspirasi mahasiswa Silira"
                            class="w-full h-auto block object-contain"
                        >
                    </div>

                    <div class="mt-5 grid grid-cols-3 gap-3">
                        <div class="rounded-[12px] bg-[#f8fafc] border border-[#0f172a]/7 px-4 py-3.5">
                            <p class="text-[12.5px] font-medium text-[#475569]">Verifikasi</p>
                            <p class="mt-1 font-mono text-[22px] font-semibold text-[#0f172a] tabular-nums">{{ number_format($stats['menunggu']) }}</p>
                        </div>
                        <div class="rounded-[12px] bg-[#eff6ff]/70 border border-[#1e3a8a]/12 px-4 py-3.5">
                            <p class="text-[12.5px] font-medium text-[#1e3a8a]">Diproses</p>
                            <p class="mt-1 font-mono text-[22px] font-semibold text-[#1e3a8a] tabular-nums">{{ number_format($stats['diproses']) }}</p>
                        </div>
                        <div class="rounded-[12px] bg-[#f8fafc] border border-[#0f172a]/7 px-4 py-3.5">
                            <p class="text-[12.5px] font-medium text-[#475569]">Selesai</p>
                            <p class="mt-1 font-mono text-[22px] font-semibold text-[#0f172a] tabular-nums">{{ number_format($stats['selesai']) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section 2: 3-Step Minimal SaaS Bento --}}
    <section class="bg-[#f8fafc] border-y border-[#0f172a]/7 py-16 lg:py-20">
        <div class="max-w-[1180px] mx-auto px-5 sm:px-8">
            <h2 class="text-[28px] sm:text-[34px] font-semibold tracking-[-0.035em] text-[#0f172a]">
                Tiga langkah, tanpa akun.
            </h2>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="s-card p-6 hover:border-[#1e3a8a]/25 transition">
                    <div class="w-10 h-10 rounded-[10px] bg-[#eff6ff] text-[#1e3a8a] flex items-center justify-center font-mono text-[13.5px] font-semibold">
                        01
                    </div>
                    <h3 class="mt-5 text-[17px] font-semibold text-[#0f172a]">Kirim laporan</h3>
                    <p class="mt-1.5 text-[14.5px] text-[#475569]">
                        Pilih bidang tujuan, tulis uraian, dan unggah bukti pendukung.
                    </p>
                </div>

                <div class="s-card p-6 hover:border-[#1e3a8a]/25 transition">
                    <div class="w-10 h-10 rounded-[10px] bg-[#eff6ff] text-[#1e3a8a] flex items-center justify-center font-mono text-[13.5px] font-semibold">
                        02
                    </div>
                    <h3 class="mt-5 text-[17px] font-semibold text-[#0f172a]">Terima tiket &amp; PIN</h3>
                    <p class="mt-1.5 text-[14.5px] text-[#475569]">
                        Kode tiket dan tanda terima PDF diterbitkan otomatis.
                    </p>
                </div>

                <div class="s-card p-6 hover:border-[#1e3a8a]/25 transition">
                    <div class="w-10 h-10 rounded-[10px] bg-[#eff6ff] text-[#1e3a8a] flex items-center justify-center font-mono text-[13.5px] font-semibold">
                        03
                    </div>
                    <h3 class="mt-5 text-[17px] font-semibold text-[#0f172a]">Pantau &amp; balas</h3>
                    <p class="mt-1.5 text-[14.5px] text-[#475569]">
                        Cek progres verifikasi DPM dan kirim klarifikasi dua arah.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Section 3: Privacy Split with Smooth Illustration --}}
    <section class="bg-white py-16 lg:py-20">
        <div class="max-w-[1180px] mx-auto px-5 sm:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                <div class="lg:col-span-6">
                    <div class="rounded-[18px] overflow-hidden bg-[#f8fafc]">
                        <img
                            src="{{ asset('images/privacy-illustration.jpg') }}"
                            alt="Ilustrasi tiga tingkat privasi laporan: Publik, Anonim, dan Rahasia"
                            class="w-full h-auto block object-cover"
                        >
                    </div>
                </div>

                <div class="lg:col-span-6">
                    <h2 class="text-[28px] sm:text-[36px] font-semibold leading-[1.12] tracking-[-0.035em] text-[#0f172a]">
                        Privasi sesuai kebutuhan.
                    </h2>
                    <p class="mt-3 text-[16px] text-[#475569]">
                        Atur visibilitas identitas Anda setiap kali mengirim laporan.
                    </p>

                    <div class="mt-7 divide-y divide-[#0f172a]/8 border-t border-b border-[#0f172a]/8">
                        <div class="py-4 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#2563eb] shrink-0" aria-hidden="true"></span>
                                <span class="font-semibold text-[#0f172a]">Publik</span>
                            </div>
                            <span class="text-[14.5px] text-[#475569] text-right">Terbuka di direktori bersama nama pelapor</span>
                        </div>
                        <div class="py-4 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#d97706] shrink-0" aria-hidden="true"></span>
                                <span class="font-semibold text-[#0f172a]">Anonim</span>
                            </div>
                            <span class="text-[14.5px] text-[#475569] text-right">Tampil di publik tanpa identitas pelapor</span>
                        </div>
                        <div class="py-4 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#dc2626] shrink-0" aria-hidden="true"></span>
                                <span class="font-semibold text-[#0f172a]">Rahasia</span>
                            </div>
                            <span class="text-[14.5px] text-[#475569] text-right">Tertutup &amp; hanya dibuka dengan PIN akses</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('reports.create') }}" class="s-link text-[15px] inline-flex items-center gap-1.5">
                            Pilih mode saat melapor &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section 4: Latest Public Aspirations --}}
    <section class="bg-[#f8fafc] border-t border-[#0f172a]/7 py-16 lg:py-20">
        <div class="max-w-[1180px] mx-auto px-5 sm:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-7">
                <div>
                    <h2 class="text-[28px] sm:text-[34px] font-semibold tracking-[-0.035em] text-[#0f172a]">
                        Aspirasi terbaru
                    </h2>
                    <p class="mt-1 text-[15px] text-[#475569]">
                        Laporan publik dan anonim yang sedang dikawal DPM FDIK.
                    </p>
                </div>
                <a href="{{ route('public.feed') }}" class="s-btn-secondary h-[40px] px-4 text-[14px] shrink-0">
                    Lihat semua &rarr;
                </a>
            </div>

            <div class="s-card overflow-hidden divide-y divide-[#0f172a]/7">
                @forelse ($publicReports as $item)
                    <a
                        href="{{ route('public.aspirations.show', $item->ticket_code) }}"
                        class="group flex flex-col md:flex-row md:items-center justify-between gap-3 px-6 py-4.5 hover:bg-[#f8fafc] transition"
                    >
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2 text-[12.5px]">
                                <span class="font-mono font-medium text-[#475569]">{{ $item->ticket_code }}</span>
                                <span class="px-2.5 py-0.5 rounded-full font-medium {{ $item->status_badge_class }}">{{ $item->status_label }}</span>
                                @if ($item->category)
                                    <span class="text-[#475569]">{{ $item->category->name }}</span>
                                @endif
                            </div>
                            <h3 class="mt-1.5 text-[16px] font-semibold text-[#0f172a] group-hover:text-[#1e3a8a] transition leading-snug break-words [overflow-wrap:anywhere]">
                                {{ $item->judul_laporan }}
                            </h3>
                        </div>

                        <div class="flex items-center gap-4 text-[13px] text-[#475569] shrink-0">
                            <span>{{ $item->public_reporter_name }}</span>
                            <span class="font-mono px-2.5 py-0.5 rounded-full bg-[#f8fafc] border border-[#0f172a]/8 text-[#0f172a]">{{ $item->public_responses_count }} log</span>
                            <span class="text-[#1e3a8a] font-medium transition-transform group-hover:translate-x-0.5" aria-hidden="true">&rarr;</span>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-12 text-center text-[15px] text-[#475569]">
                        Belum ada laporan publik yang tercatat.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
