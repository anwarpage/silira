@extends('layouts.public')

@section('title', 'Silira — Portal aspirasi Fakultas Dakwah dan Ilmu Komunikasi')

@section('content')
    @php
        $featured = $publicReports->first();
        $featuredResponses = $featured ? $featured->publicResponses->take(-2) : collect();
        $stepOrder = ['menunggu' => 1, 'diverifikasi' => 2, 'diproses' => 3, 'selesai' => 4, 'ditolak' => 0];
        $featuredStep = $stepOrder[$featured?->status ?? 'selesai'] ?? 4;
    @endphp

    {{-- Section 1: Hero + Real Live Product App-Frame --}}
    <section class="py-12 lg:py-16">
        <div class="max-w-[1200px] mx-auto px-6 sm:px-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                <div class="lg:col-span-5">
                    <p class="text-[14px] font-medium text-[#475569]">
                        DPM Fakultas Dakwah dan Ilmu Komunikasi &middot; {{ number_format($stats['total']) }} laporan tercatat
                    </p>

                    <h1 class="mt-4 text-[38px] sm:text-[48px] font-medium leading-[1.08] tracking-[-0.035em] text-[#0f172a]">
                        Portal aspirasi fakultas yang terlacak.
                    </h1>

                    <p class="mt-5 text-[17px] leading-[1.55] text-[#475569]">
                        Sampaikan aspirasi atau pengaduan tanpa login. Terima kode tiket untuk memantau status dan membalas tindak lanjut DPM.
                    </p>

                    <div class="mt-7 flex flex-wrap items-center gap-3">
                        <a href="{{ route('reports.create') }}" class="s-btn-primary">
                            Kirim aspirasi
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </a>
                        <a href="{{ route('tracking.index') }}" class="s-btn-secondary">
                            Lacak tiket
                        </a>
                    </div>

                    {{-- Inline Quick Track --}}
                    <form action="{{ route('tracking.lookup') }}" method="POST" class="mt-7 pt-6 border-t border-[#0f172a]/12">
                        @csrf
                        <label for="quick_ticket" class="block text-[13px] font-medium text-[#475569] mb-2">Sudah memiliki kode tiket?</label>
                        <div class="flex gap-2">
                            <input
                                type="text"
                                name="ticket_code"
                                id="quick_ticket"
                                placeholder="SLR-2026-FDIK01"
                                class="s-input font-mono uppercase placeholder:normal-case placeholder:font-sans"
                                required
                            >
                            <button type="submit" class="s-btn-secondary shrink-0 h-[48px]">
                                Buka
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Real Live Product UI Frame --}}
                <div class="lg:col-span-7">
                    <div class="rounded-[14px] border border-[#0f172a]/14 bg-white shadow-sm overflow-hidden">
                        <div class="px-5 py-3.5 bg-[#0f172a] text-[#f5f7fa] flex items-center justify-between gap-4 text-[13px]">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="font-mono font-medium">{{ $featured?->ticket_code ?? 'SLR-2026-FDIK01' }}</span>
                                <span class="text-[#94a3b8]">/</span>
                                <span class="truncate text-[#94a3b8]">{{ $featured?->category?->name ?? 'Fasilitas & Sarana Prasarana' }}</span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-[6px] text-[13px] font-medium bg-[#1e3a8a] text-white shrink-0">
                                {{ $featured?->status_label ?? 'Selesai' }}
                            </span>
                        </div>

                        <div class="p-6 sm:p-7">
                            <h2 class="text-[20px] font-medium leading-snug text-[#0f172a]">
                                {{ $featured?->judul_laporan ?? 'Proyektor dan AC Ruang Kuliah B.204 Tidak Berfungsi' }}
                            </h2>
                            <p class="mt-1.5 text-[14px] text-[#475569]">
                                {{ $featured?->public_reporter_name ?? 'Ahmad Fauzi' }} &middot; {{ $featured?->jenis_laporan ?? 'Pengaduan' }} &middot; {{ $featured?->privacy_label ?? 'Publik' }}
                            </p>

                            <div class="mt-6 grid grid-cols-4 gap-2">
                                @foreach (['Menunggu', 'Diverifikasi', 'Diproses', 'Selesai'] as $i => $stepName)
                                    <div>
                                        <div class="h-1.5 rounded-full {{ ($i + 1) <= $featuredStep ? 'bg-[#1e3a8a]' : 'bg-[#eceff4]' }}"></div>
                                        <span class="block mt-2 text-[13px] {{ ($i + 1) <= $featuredStep ? 'font-medium text-[#0f172a]' : 'text-[#475569]' }}">{{ $stepName }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6 pt-5 border-t border-[#0f172a]/10 space-y-3.5">
                                @forelse ($featuredResponses as $res)
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <p class="text-[13px] font-medium text-[#0f172a]">
                                                {{ $res->sender_name }}
                                                <span class="text-[#475569] font-normal">&middot; {{ $res->sender_type === 'admin' ? 'Admin DPM' : ($res->sender_type === 'reporter' ? 'Pelapor' : 'Sistem') }}</span>
                                            </p>
                                            <p class="mt-1 text-[15px] text-[#0f172a]/85 leading-relaxed">
                                                {{ \Illuminate\Support\Str::limit($res->message, 150) }}
                                            </p>
                                        </div>
                                        <time class="font-mono text-[13px] text-[#475569] shrink-0">{{ $res->created_at->format('d M') }}</time>
                                    </div>
                                @empty
                                    <p class="text-[15px] text-[#475569]">Belum ada riwayat tindak lanjut.</p>
                                @endforelse
                            </div>

                            @if ($featured)
                                <div class="mt-6 pt-4 border-t border-[#0f172a]/10 flex items-center justify-between text-[14px]">
                                    <span class="text-[#475569]">{{ $stats['selesai'] }} selesai &middot; {{ $stats['diproses'] }} diproses &middot; {{ $stats['menunggu'] }} verifikasi</span>
                                    <a href="{{ route('tracking.show', $featured->ticket_code) }}" class="font-medium text-[#1e3a8a] hover:underline">
                                        Buka tiket &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section 2: Institutional Mechanics & Privacy Matrix (Ground Shift #eceff4) --}}
    <section class="bg-[#eceff4] border-y border-[#0f172a]/10 py-16 lg:py-20">
        <div class="max-w-[1200px] mx-auto px-6 sm:px-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
                {{-- Left: Clear Institutional Value Points --}}
                <div class="lg:col-span-6">
                    <h2 class="text-[28px] sm:text-[34px] font-medium leading-[1.15] text-[#0f172a]">
                        Dirancang untuk tindak lanjut, bukan formulir satu arah.
                    </h2>
                    <p class="mt-3 text-[16px] text-[#475569] leading-relaxed">
                        Setiap laporan dicatat sebagai tiket bernomor yang dapat dipantau pelapor, dibalas dua arah, dan diarsipkan rapi lintas kepengurusan DPM.
                    </p>

                    <div class="mt-8 divide-y divide-[#0f172a]/12 border-t border-[#0f172a]/12">
                        <div class="py-5 flex items-start gap-4">
                            <div class="w-9 h-9 rounded-[8px] bg-white border border-[#0f172a]/12 flex items-center justify-center text-[#1e3a8a] shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-[16px] font-medium text-[#0f172a]">Pelacakan status tanpa akun</h3>
                                <p class="mt-1 text-[15px] text-[#475569] leading-relaxed">
                                    Kode tiket unik terbit otomatis setelah melapor agar mahasiswa selalu tahu progres verifikasi hingga penyelesaian.
                                </p>
                            </div>
                        </div>

                        <div class="py-5 flex items-start gap-4">
                            <div class="w-9 h-9 rounded-[8px] bg-white border border-[#0f172a]/12 flex items-center justify-center text-[#1e3a8a] shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-[16px] font-medium text-[#0f172a]">Interaksi dua arah dalam satu alur</h3>
                                <p class="mt-1 text-[15px] text-[#475569] leading-relaxed">
                                    Pelapor dapat mengirim klarifikasi atau bukti tambahan langsung pada halaman tiket yang sama, tanpa pesan tercecer.
                                </p>
                            </div>
                        </div>

                        <div class="py-5 flex items-start gap-4">
                            <div class="w-9 h-9 rounded-[8px] bg-white border border-[#0f172a]/12 flex items-center justify-center text-[#1e3a8a] shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-[16px] font-medium text-[#0f172a]">Catatan internal &amp; estafet kepengurusan</h3>
                                <p class="mt-1 text-[15px] text-[#475569] leading-relaxed">
                                    Admin DPM memiliki catatan koordinasi internal terpisah dari publik serta ekspor rekap sesuai filter kapan pun dibutuhkan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Real Privacy Access Matrix UI Card --}}
                <div class="lg:col-span-6">
                    <div class="bg-white rounded-[12px] border border-[#0f172a]/12 overflow-hidden shadow-2xs">
                        <div class="px-6 py-4 border-b border-[#0f172a]/10 bg-[#f8fafc] flex items-center justify-between">
                            <span class="text-[14px] font-medium text-[#0f172a]">Kontrol privasi tiga tingkat</span>
                            <span class="font-mono text-[13px] text-[#475569]">Hak akses laporan</span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-[14px]">
                                <thead class="border-b border-[#0f172a]/10 text-[13px] font-medium text-[#475569]">
                                    <tr>
                                        <th class="px-6 py-3.5">Mode privasi</th>
                                        <th class="px-4 py-3.5 text-center">Feed publik</th>
                                        <th class="px-4 py-3.5 text-center">Nama pelapor</th>
                                        <th class="px-6 py-3.5 text-center">Admin DPM</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#0f172a]/10">
                                    <tr>
                                        <td class="px-6 py-4">
                                            <p class="font-medium text-[#0f172a] flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-[#16a34a]"></span>
                                                Publik
                                            </p>
                                            <p class="text-[13px] text-[#475569] mt-0.5">Aspirasi terbuka untuk umum</p>
                                        </td>
                                        <td class="px-4 py-4 text-center text-[#166534] font-medium">Tampil</td>
                                        <td class="px-4 py-4 text-center text-[#0f172a]">Terbuka</td>
                                        <td class="px-6 py-4 text-center text-[#1e3a8a] font-medium">Penuh</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4">
                                            <p class="font-medium text-[#0f172a] flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-[#d97706]"></span>
                                                Anonim
                                            </p>
                                            <p class="text-[13px] text-[#475569] mt-0.5">Identitas dirahasiakan dari publik</p>
                                        </td>
                                        <td class="px-4 py-4 text-center text-[#166534] font-medium">Tampil</td>
                                        <td class="px-4 py-4 text-center text-[#92400e] font-medium">Disamarkan</td>
                                        <td class="px-6 py-4 text-center text-[#1e3a8a] font-medium">Penuh</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4">
                                            <p class="font-medium text-[#0f172a] flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full bg-[#dc2626]"></span>
                                                Rahasia
                                            </p>
                                            <p class="text-[13px] text-[#475569] mt-0.5">Khusus aduan sensitif &amp; tertutup</p>
                                        </td>
                                        <td class="px-4 py-4 text-center text-[#475569]">Tersembunyi</td>
                                        <td class="px-4 py-4 text-center text-[#991b1b] font-medium">Tertutup</td>
                                        <td class="px-6 py-4 text-center text-[#1e3a8a] font-medium">Penuh</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="px-6 py-4 bg-[#f8fafc] border-t border-[#0f172a]/10 flex items-center justify-between text-[13px] text-[#475569]">
                            <span>Seluruh mode tetap mendapat kode tiket pelacakan.</span>
                            <a href="{{ route('reports.create') }}" class="font-medium text-[#1e3a8a] hover:underline shrink-0 ml-3">Pilih saat melapor &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Section 3: Framed Public Aspirations Directory --}}
    <section class="py-16 lg:py-20">
        <div class="max-w-[1200px] mx-auto px-6 sm:px-10">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-[28px] sm:text-[32px] font-medium leading-tight text-[#0f172a]">Aspirasi publik terbaru</h2>
                    <p class="mt-1.5 text-[15px] text-[#475569]">Daftar laporan terbuka dan anonim yang sedang dikawal DPM FDIK.</p>
                </div>
                <a href="{{ route('public.feed') }}" class="s-btn-secondary h-[40px] px-4 text-[14px]">
                    Lihat semua aspirasi &rarr;
                </a>
            </div>

            <div class="bg-white rounded-[12px] border border-[#0f172a]/12 overflow-hidden">
                {{-- Directory Summary Bar --}}
                <div class="px-6 py-3.5 bg-[#f8fafc] border-b border-[#0f172a]/10 flex flex-wrap items-center justify-between gap-3 text-[13px] text-[#475569]">
                    <div class="flex flex-wrap items-center gap-4">
                        <span>Total: <strong class="font-mono text-[#0f172a]">{{ number_format($stats['total']) }}</strong></span>
                        <span>Verifikasi: <strong class="font-mono text-[#92400e]">{{ number_format($stats['menunggu']) }}</strong></span>
                        <span>Diproses: <strong class="font-mono text-[#1e3a8a]">{{ number_format($stats['diproses']) }}</strong></span>
                        <span>Selesai: <strong class="font-mono text-[#166534]">{{ number_format($stats['selesai']) }}</strong></span>
                    </div>
                    <span>Laporan Rahasia tidak dipublikasikan</span>
                </div>

                {{-- Directory Rows --}}
                <div class="divide-y divide-[#0f172a]/10">
                    @forelse ($publicReports as $item)
                        <a href="{{ route('tracking.show', $item->ticket_code) }}" class="group px-6 py-4.5 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-[#f8fafc] transition">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2.5 text-[13px]">
                                    <span class="font-mono font-medium text-[#475569]">{{ $item->ticket_code }}</span>
                                    <span class="px-2.5 py-0.5 rounded-[6px] font-medium {{ $item->status_badge_class }}">{{ $item->status_label }}</span>
                                    <span class="text-[#475569]">{{ $item->jenis_laporan }}</span>
                                    @if ($item->category)
                                        <span class="text-[#475569]">&middot; {{ $item->category->name }}</span>
                                    @endif
                                </div>
                                <h3 class="mt-1.5 text-[16px] font-medium text-[#0f172a] group-hover:text-[#1e3a8a] transition leading-snug">
                                    {{ $item->judul_laporan }}
                                </h3>
                            </div>

                            <div class="flex items-center gap-4 text-[13px] text-[#475569] shrink-0">
                                <span>{{ $item->public_reporter_name }}</span>
                                <span class="font-mono px-2 py-0.5 rounded bg-[#f5f7fa] border border-[#0f172a]/8 text-[#0f172a]">{{ $item->public_responses_count }} log</span>
                                <span class="text-[#1e3a8a] font-medium transition-transform group-hover:translate-x-0.5">&rarr;</span>
                            </div>
                        </a>
                    @empty
                        <div class="px-6 py-12 text-center text-[15px] text-[#475569]">
                            Belum ada laporan publik yang masuk.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
@endsection
