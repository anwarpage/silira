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
                    <p class="text-[14px] font-medium text-[#65564c]">
                        DPM Fakultas Dakwah dan Ilmu Komunikasi &middot; {{ number_format($stats['total']) }} laporan tercatat
                    </p>

                    <h1 class="mt-4 text-[38px] sm:text-[48px] font-medium leading-[1.08] tracking-[-0.035em] text-[#2a1d17]">
                        Portal aspirasi fakultas yang terlacak.
                    </h1>

                    <p class="mt-5 text-[17px] leading-[1.55] text-[#65564c]">
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
                    <form action="{{ route('tracking.lookup') }}" method="POST" class="mt-7 pt-6 border-t border-[#2a1d17]/12">
                        @csrf
                        <label for="quick_ticket" class="block text-[13px] font-medium text-[#65564c] mb-2">Sudah memiliki kode tiket?</label>
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
                    <div class="rounded-[14px] border border-[#2a1d17]/16 bg-[#f6f4ef] overflow-hidden">
                        {{-- Window Chrome --}}
                        <div class="px-5 py-3.5 bg-[#2a1d17] text-[#e5e1d8] flex items-center justify-between gap-4 text-[13px]">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="font-mono font-medium">{{ $featured?->ticket_code ?? 'SLR-2026-FDIK01' }}</span>
                                <span class="text-[#a89f96]">/</span>
                                <span class="truncate text-[#a89f96]">{{ $featured?->category?->name ?? 'Fasilitas & Sarana Prasarana' }}</span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-[6px] text-[13px] font-medium bg-[#456f3a] text-white shrink-0">
                                {{ $featured?->status_label ?? 'Selesai' }}
                            </span>
                        </div>

                        <div class="p-6 sm:p-7">
                            <h2 class="text-[20px] font-medium leading-snug text-[#2a1d17]">
                                {{ $featured?->judul_laporan ?? 'Proyektor dan AC Ruang Kuliah B.204 Tidak Berfungsi' }}
                            </h2>
                            <p class="mt-1.5 text-[14px] text-[#65564c]">
                                {{ $featured?->public_reporter_name ?? 'Ahmad Fauzi' }} &middot; {{ $featured?->jenis_laporan ?? 'Pengaduan' }} &middot; {{ $featured?->privacy_label ?? 'Publik' }}
                            </p>

                            {{-- 4-Stage Status Bar --}}
                            <div class="mt-6 grid grid-cols-4 gap-2">
                                @foreach (['Menunggu', 'Diverifikasi', 'Diproses', 'Selesai'] as $i => $stepName)
                                    <div>
                                        <div class="h-1.5 rounded-full {{ ($i + 1) <= $featuredStep ? 'bg-[#456f3a]' : 'bg-[#dad5ca]' }}"></div>
                                        <span class="block mt-2 text-[13px] {{ ($i + 1) <= $featuredStep ? 'font-medium text-[#2a1d17]' : 'text-[#65564c]' }}">{{ $stepName }}</span>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Live Response Thread --}}
                            <div class="mt-6 pt-5 border-t border-[#2a1d17]/12 space-y-3.5">
                                @forelse ($featuredResponses as $res)
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0">
                                            <p class="text-[13px] font-medium text-[#2a1d17]">
                                                {{ $res->sender_name }}
                                                <span class="text-[#65564c] font-normal">&middot; {{ $res->sender_type === 'admin' ? 'Admin DPM' : ($res->sender_type === 'reporter' ? 'Pelapor' : 'Sistem') }}</span>
                                            </p>
                                            <p class="mt-1 text-[15px] text-[#2a1d17]/85 leading-relaxed">
                                                {{ \Illuminate\Support\Str::limit($res->message, 150) }}
                                            </p>
                                        </div>
                                        <time class="font-mono text-[13px] text-[#65564c] shrink-0">{{ $res->created_at->format('d M') }}</time>
                                    </div>
                                @empty
                                    <p class="text-[15px] text-[#65564c]">Belum ada riwayat tindak lanjut.</p>
                                @endforelse
                            </div>

                            @if ($featured)
                                <div class="mt-6 pt-4 border-t border-[#2a1d17]/12 flex items-center justify-between text-[14px]">
                                    <span class="text-[#65564c]">{{ $stats['selesai'] }} selesai &middot; {{ $stats['diproses'] }} diproses &middot; {{ $stats['menunggu'] }} verifikasi</span>
                                    <a href="{{ route('tracking.show', $featured->ticket_code) }}" class="font-medium text-[#456f3a] hover:underline">
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

    {{-- Section 2: Why Silira (Ground Shift to --p-surface #dad5ca) --}}
    <section class="bg-[#dad5ca] py-16 lg:py-20">
        <div class="max-w-[1200px] mx-auto px-6 sm:px-10">
            <div class="max-w-2xl">
                <h2 class="text-[28px] sm:text-[34px] font-medium leading-[1.15] text-[#2a1d17]">
                    Lebih dari sekadar formulir satu arah.
                </h2>
                <p class="mt-3 text-[16px] text-[#65564c] leading-relaxed">
                    Google Form berhenti di tombol kirim. Silira menjaga kepastian status, komunikasi dua arah, dan arsip tindak lanjut lintas kepengurusan DPM.
                </p>
            </div>

            <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="s-card p-7">
                    <p class="font-mono text-[13px] text-[#456f3a] font-medium">01</p>
                    <h3 class="mt-3 text-[18px] font-medium text-[#2a1d17]">Pelacakan kode tiket</h3>
                    <p class="mt-2 text-[15px] text-[#65564c] leading-relaxed">
                        Setiap laporan langsung menerima kode unik untuk memantau status dari verifikasi hingga tuntas tanpa membuat akun.
                    </p>
                </div>

                <div class="s-card p-7">
                    <p class="font-mono text-[13px] text-[#456f3a] font-medium">02</p>
                    <h3 class="mt-3 text-[18px] font-medium text-[#2a1d17]">Komunikasi dua arah</h3>
                    <p class="mt-2 text-[15px] text-[#65564c] leading-relaxed">
                        Pelapor dapat membalas klarifikasi atau menambahkan lampiran bukti langsung di halaman tiket yang sama.
                    </p>
                </div>

                <div class="s-card p-7">
                    <p class="font-mono text-[13px] text-[#456f3a] font-medium">03</p>
                    <h3 class="mt-3 text-[18px] font-medium text-[#2a1d17]">Privasi tiga tingkat</h3>
                    <p class="mt-2 text-[15px] text-[#65564c] leading-relaxed">
                        Pilih Publik, Anonim, atau Rahasia agar aduan sensitif tetap aman sementara aspirasi umum dapat dipantau bersama.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Section 3: Public Feed Rows (Clean Hairlines, No Nested Boxes) --}}
    <section class="py-16 lg:py-20">
        <div class="max-w-[1200px] mx-auto px-6 sm:px-10">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-6 border-b border-[#2a1d17]/15">
                <div>
                    <h2 class="text-[28px] sm:text-[32px] font-medium leading-tight text-[#2a1d17]">Aspirasi publik terbaru</h2>
                    <p class="mt-1.5 text-[15px] text-[#65564c]">Laporan berstatus Rahasia tidak ditampilkan pada daftar ini.</p>
                </div>
                <a href="{{ route('public.feed') }}" class="text-[15px] font-medium text-[#456f3a] hover:underline">
                    Lihat semua aspirasi &rarr;
                </a>
            </div>

            <div class="divide-y divide-[#2a1d17]/13">
                @forelse ($publicReports as $item)
                    <a href="{{ route('tracking.show', $item->ticket_code) }}" class="py-5 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-[#dad5ca]/45 px-3 -mx-3 rounded-[8px] transition">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2.5 text-[13px]">
                                <span class="font-mono text-[#65564c]">{{ $item->ticket_code }}</span>
                                <span class="px-2.5 py-0.5 rounded-[6px] font-medium {{ $item->status_badge_class }}">{{ $item->status_label }}</span>
                                <span class="text-[#65564c]">{{ $item->jenis_laporan }}</span>
                                @if ($item->category)
                                    <span class="text-[#65564c]">&middot; {{ $item->category->name }}</span>
                                @endif
                            </div>
                            <h3 class="mt-2 text-[17px] font-medium text-[#2a1d17] leading-snug">{{ $item->judul_laporan }}</h3>
                        </div>

                        <div class="flex items-center gap-4 text-[14px] text-[#65564c] shrink-0">
                            <span>{{ $item->public_reporter_name }}</span>
                            <span>&middot;</span>
                            <span class="font-mono text-[13px]">{{ $item->public_responses_count }} progres</span>
                        </div>
                    </a>
                @empty
                    <div class="py-12 text-center text-[15px] text-[#65564c]">
                        Belum ada laporan publik yang masuk.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
