@extends('layouts.public')

@section('title', 'SILIRA — Portal Aspirasi & Pengaduan DPM FDIK')

@section('content')
    {{-- Compact Hero + Integrated Track Bar & Inline Metrics --}}
    <section class="bg-white border-b border-stone-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-12 pb-10 sm:pt-16 sm:pb-12">
            <div class="max-w-2xl">
                <p class="text-xs font-medium text-emerald-800 tracking-wide">DPM Fakultas Dakwah dan Ilmu Komunikasi</p>
                <h1 class="mt-2.5 text-3xl sm:text-4xl font-bold tracking-[-0.03em] text-stone-950 leading-[1.15]">
                    Layanan aspirasi dan pengaduan mahasiswa yang terlacak hingga tuntas.
                </h1>
                <p class="mt-3.5 text-sm sm:text-base text-stone-600 leading-relaxed">
                    Kirim laporan secara terbuka, anonim, atau rahasia tanpa perlu membuat akun. Setiap laporan mendapatkan kode tiket untuk memantau status dan membalas tindak lanjut DPM.
                </p>

                <div class="mt-6 flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                    <a href="{{ route('reports.create') }}" class="btn-press inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-emerald-900 text-white text-sm font-semibold hover:bg-emerald-950">
                        Kirim Aspirasi
                    </a>

                    <form action="{{ route('tracking.lookup') }}" method="POST" class="flex-1 flex items-center gap-2 max-w-md">
                        @csrf
                        <input
                            type="text"
                            name="ticket_code"
                            placeholder="Lacak kode tiket (mis. SLR-2026-FDIK01)"
                            class="flex-1 px-3.5 py-2.5 rounded-lg border border-stone-300 bg-stone-50/70 text-sm font-mono uppercase placeholder:normal-case placeholder:font-sans text-stone-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-900/15 focus:border-emerald-800"
                            required
                        >
                        <button type="submit" class="btn-press px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-stone-800 text-sm font-medium hover:bg-stone-50 cursor-pointer">
                            Lacak
                        </button>
                    </form>
                </div>
            </div>

            {{-- Compact Inline Metrics Bar --}}
            <div class="mt-10 pt-7 border-t border-stone-200/80 grid grid-cols-2 sm:grid-cols-4 gap-6">
                <div>
                    <p class="text-xs text-stone-500">Total laporan</p>
                    <p class="mt-1 text-2xl font-bold tracking-tight text-stone-950 font-mono tabular-nums">{{ number_format($stats['total']) }}</p>
                </div>
                <div>
                    <p class="text-xs text-stone-500">Menunggu verifikasi</p>
                    <p class="mt-1 text-2xl font-bold tracking-tight text-stone-950 font-mono tabular-nums">{{ number_format($stats['menunggu']) }}</p>
                </div>
                <div>
                    <p class="text-xs text-stone-500">Sedang diproses</p>
                    <p class="mt-1 text-2xl font-bold tracking-tight text-stone-950 font-mono tabular-nums">{{ number_format($stats['diproses']) }}</p>
                </div>
                <div>
                    <p class="text-xs text-stone-500">Selesai ditindaklanjuti</p>
                    <p class="mt-1 text-2xl font-bold tracking-tight text-emerald-800 font-mono tabular-nums">{{ number_format($stats['selesai']) }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Why SILIRA (Concise 3-Column Strip, No Bloat) --}}
    <section class="max-w-5xl mx-auto px-4 sm:px-6 mt-10">
        <div class="bg-white rounded-xl border border-stone-200 grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-stone-200">
            <div class="p-5 sm:p-6">
                <p class="text-xs font-semibold text-stone-950">Kode tiket, bukan formulir buntu</p>
                <p class="mt-1.5 text-xs text-stone-600 leading-relaxed">
                    Berbeda dari Google Form yang berhenti setelah submit, setiap laporan memiliki kode tiket untuk memantau status real-time tanpa login.
                </p>
            </div>
            <div class="p-5 sm:p-6">
                <p class="text-xs font-semibold text-stone-950">Interaksi 2 arah &amp; kontrol privasi</p>
                <p class="mt-1.5 text-xs text-stone-600 leading-relaxed">
                    Pilih mode Publik, Anonim, atau Rahasia. Pelapor dapat mengirim bukti atau klarifikasi tambahan langsung di halaman tiket.
                </p>
            </div>
            <div class="p-5 sm:p-6">
                <p class="text-xs font-semibold text-stone-950">Transparansi &amp; arsip lintas periode</p>
                <p class="mt-1.5 text-xs text-stone-600 leading-relaxed">
                    Riwayat tindak lanjut dan catatan internal DPM terdokumentasi rapi sehingga tidak hilang saat pergantian kepengurusan.
                </p>
            </div>
        </div>
    </section>

    {{-- Latest Public Aspirations (Clean Issue-Style List) --}}
    <section class="max-w-5xl mx-auto px-4 sm:px-6 mt-10">
        <div class="flex items-center justify-between mb-3.5">
            <div>
                <h2 class="text-sm font-semibold text-stone-950">Aspirasi &amp; Pengaduan Publik Terbaru</h2>
                <p class="text-xs text-stone-500">Laporan berstatus Rahasia tidak ditampilkan di daftar ini.</p>
            </div>
            <a href="{{ route('public.feed') }}" class="text-xs font-semibold text-emerald-900 hover:underline">
                Lihat semua &rarr;
            </a>
        </div>

        <div class="bg-white rounded-xl border border-stone-200 divide-y divide-stone-200 overflow-hidden">
            @forelse ($publicReports->take(5) as $item)
                <a href="{{ route('tracking.show', $item->ticket_code) }}" class="block px-5 py-4 hover:bg-stone-50/80 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2 text-[11px]">
                                <span class="font-mono font-medium text-stone-500">{{ $item->ticket_code }}</span>
                                <span class="px-2 py-0.5 rounded-md font-medium {{ $item->status_badge_class }}">{{ $item->status_label }}</span>
                                <span class="text-stone-500">{{ $item->jenis_laporan }}</span>
                                @if ($item->category)
                                    <span class="text-stone-400">&middot;</span>
                                    <span class="text-stone-500">{{ $item->category->name }}</span>
                                @endif
                            </div>
                            <p class="mt-1.5 text-sm font-semibold text-stone-950 truncate">{{ $item->judul_laporan }}</p>
                        </div>

                        <div class="flex items-center gap-3 text-xs text-stone-500 shrink-0">
                            <span>{{ $item->public_reporter_name }}</span>
                            <span>&middot;</span>
                            <span>{{ $item->created_at->diffForHumans() }}</span>
                            <span class="font-mono text-[11px] px-2 py-0.5 rounded bg-stone-100 text-stone-700">{{ $item->public_responses_count }} log</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-6 py-10 text-center text-sm text-stone-500">
                    Belum ada laporan publik yang masuk.
                </div>
            @endforelse
        </div>
    </section>
@endsection
