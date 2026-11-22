@extends('layouts.public')

@section('title', 'SILIRA — Layanan Aspirasi & Pengaduan Fakultas Dakwah dan Ilmu Komunikasi')

@section('content')
    {{-- Hero Section --}}
    <section class="bg-gradient-to-b from-red-800 via-red-700 to-red-800 text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-14 sm:py-20">
            <div class="max-w-3xl">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-red-50 text-xs font-semibold tracking-wide uppercase">
                    Layanan Aspirasi Resmi DPM FDIK
                </span>
                <h1 class="mt-4 text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight">
                    Sampaikan Aspirasi &amp; Pengaduan Anda untuk Fakultas yang Lebih Baik
                </h1>
                <p class="mt-4 text-red-100 text-base sm:text-lg leading-relaxed max-w-2xl">
                    Tidak perlu membuat akun. Kirim aspirasi, pengaduan, atau permintaan informasi secara terbuka, anonim, maupun rahasia — lalu pantau tindak lanjutnya dengan kode tiket unik.
                </p>

                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('reports.create') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-white text-red-800 font-bold text-sm shadow-sm hover:bg-red-50 transition">
                        Tulis Laporan Sekarang
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                    <a href="{{ route('public.feed') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-red-900/50 text-white border border-white/20 font-semibold text-sm hover:bg-red-900/70 transition">
                        Lihat Aspirasi Publik
                    </a>
                </div>
            </div>

            {{-- Quick Ticket Tracking Bar --}}
            <div class="mt-10 bg-white rounded-2xl p-4 sm:p-5 shadow-lg text-slate-900 max-w-2xl">
                <form action="{{ route('tracking.lookup') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <div class="flex-1">
                        <label for="ticket_code" class="sr-only">Kode Tiket</label>
                        <input
                            type="text"
                            name="ticket_code"
                            id="ticket_code"
                            placeholder="Sudah melapor? Masukkan kode tiket (contoh: SLR-2026-FDIK01)"
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600 uppercase"
                            required
                        >
                    </div>
                    <button type="submit" class="px-6 py-3 rounded-xl bg-slate-900 text-white font-semibold text-sm hover:bg-slate-800 transition whitespace-nowrap cursor-pointer">
                        Lacak Status
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- Transparency Stats --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 -mt-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Laporan</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-900 tabular-nums">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <p class="text-xs font-semibold uppercase tracking-wider text-amber-600">Menunggu / Verifikasi</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-900 tabular-nums">{{ number_format($stats['menunggu']) }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600">Sedang Diproses</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-900 tabular-nums">{{ number_format($stats['diproses']) }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-xs">
                <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600">Tuntas / Selesai</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-900 tabular-nums">{{ number_format($stats['selesai']) }}</p>
            </div>
        </div>
    </section>

    {{-- 4-Step Process --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 mt-14">
        <div class="text-center max-w-xl mx-auto">
            <h2 class="text-2xl font-extrabold text-slate-900">Alur Pengelolaan Aspirasi</h2>
            <p class="mt-2 text-sm text-slate-600">Transparan dari saat Anda mengirim laporan hingga tuntas ditindaklanjuti oleh DPM FDIK.</p>
        </div>

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach ([
                ['01', 'Tulis Laporan', 'Isi form aspirasi atau pengaduan lengkap dengan kategori dan opsi privasi tanpa perlu login.'],
                ['02', 'Verifikasi DPM', 'Komisi Aspirasi DPM memverifikasi kelengkapan data dan meneruskan ke pihak fakultas terkait.'],
                ['03', 'Tindak Lanjut', 'Proses audiensi atau penanganan dicatat secara berkala dan dapat dibalas langsung via kode tiket.'],
                ['04', 'Selesai', 'Hasil akhir penanganan diumumkan secara resmi pada halaman tiket laporan Anda.'],
            ] as [$num, $title, $desc])
                <div class="bg-white rounded-2xl border border-slate-200 p-6">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-red-50 text-red-700 font-extrabold text-sm">{{ $num }}</span>
                    <h3 class="mt-4 font-bold text-slate-900">{{ $title }}</h3>
                    <p class="mt-1.5 text-sm text-slate-600 leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Public Feed Preview --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 mt-16">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">Aspirasi &amp; Pengaduan Publik Terbaru</h2>
                <p class="mt-1 text-sm text-slate-600">Laporan berstatus Rahasia tidak ditampilkan di sini demi menjaga privasi pelapor.</p>
            </div>
            <a href="{{ route('public.feed') }}" class="text-sm font-semibold text-red-700 hover:text-red-800 transition">
                Lihat Semua Laporan &rarr;
            </a>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
            @forelse ($publicReports as $item)
                <article class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col justify-between hover:border-slate-300 transition">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="px-2.5 py-1 rounded-full font-semibold {{ $item->status_badge_class }}">{{ $item->status_label }}</span>
                            <span class="px-2.5 py-1 rounded-full font-medium bg-slate-100 text-slate-700">{{ $item->jenis_laporan }}</span>
                            @if ($item->category)
                                <span class="text-slate-500 font-medium">{{ $item->category->name }}</span>
                            @endif
                        </div>

                        <h3 class="mt-3 font-bold text-slate-900 text-base leading-snug">
                            <a href="{{ route('tracking.show', $item->ticket_code) }}" class="hover:text-red-700 transition">
                                {{ $item->judul_laporan }}
                            </a>
                        </h3>
                        <p class="mt-2 text-sm text-slate-600 line-clamp-3 leading-relaxed">
                            {{ \Illuminate\Support\Str::limit($item->isi_laporan, 180) }}
                        </p>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <div>
                            <span class="font-semibold text-slate-700">{{ $item->public_reporter_name }}</span>
                            <span class="mx-1.5">&middot;</span>
                            <span>{{ $item->created_at->diffForHumans() }}</span>
                        </div>
                        <a href="{{ route('tracking.show', $item->ticket_code) }}" class="font-semibold text-red-700 hover:underline">
                            {{ $item->public_responses_count }} progres &rarr;
                        </a>
                    </div>
                </article>
            @empty
                <div class="md:col-span-2 bg-white rounded-2xl border border-slate-200 p-10 text-center text-slate-500 text-sm">
                    Belum ada laporan publik yang masuk. Jadilah yang pertama menyampaikan aspirasi Anda.
                </div>
            @endforelse
        </div>
    </section>
@endsection
