@extends('layouts.admin')

@section('title', 'Dashboard Admin — SILIRA')

@section('content')
    @php
        $total = max(1, $stats['total']);
        $completionRate = $stats['total'] > 0 ? round(($stats['selesai'] / $stats['total']) * 100) : 0;
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-red-700">Ringkasan Operasional</span>
            <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-950">Dashboard Admin DPM</h1>
            <p class="text-sm text-slate-500 mt-1">Pantau laporan masuk, antrean verifikasi, dan kinerja penyelesaian aspirasi FDIK.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.reports.index', ['status' => 'menunggu']) }}" class="btn-press px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-slate-800 text-xs font-bold hover:bg-slate-50">
                Antrean Verifikasi ({{ $stats['menunggu'] }})
            </a>
            <a href="{{ route('admin.reports.export') }}" class="btn-press px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800">
                Export CSV
            </a>
        </div>
    </div>

    {{-- Top KPI Cards --}}
    <div class="mt-7 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/90 p-5">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-slate-500">Total Laporan</p>
                <span class="w-2 h-2 rounded-full bg-slate-900"></span>
            </div>
            <p class="mt-2.5 text-3xl font-extrabold text-slate-950 tabular-nums">{{ number_format($stats['total']) }}</p>
            <p class="mt-1 text-xs text-slate-500">Semua klasifikasi &amp; privasi</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/90 p-5">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-amber-700">Perlu Verifikasi</p>
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            </div>
            <p class="mt-2.5 text-3xl font-extrabold text-slate-950 tabular-nums">{{ number_format($stats['menunggu']) }}</p>
            <p class="mt-1 text-xs text-slate-500">Menunggu pemeriksaan awal DPM</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/90 p-5">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-indigo-700">Aktif Ditangani</p>
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
            </div>
            <p class="mt-2.5 text-3xl font-extrabold text-slate-950 tabular-nums">{{ number_format($stats['diverifikasi'] + $stats['diproses']) }}</p>
            <p class="mt-1 text-xs text-slate-500">{{ $stats['diverifikasi'] }} diverifikasi &middot; {{ $stats['diproses'] }} diproses</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/90 p-5">
            <div class="flex items-center justify-between">
                <p class="text-xs font-semibold text-emerald-700">Tingkat Penyelesaian</p>
                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 tabular-nums">{{ $completionRate }}%</span>
            </div>
            <p class="mt-2.5 text-3xl font-extrabold text-slate-950 tabular-nums">{{ number_format($stats['selesai']) }}</p>
            <p class="mt-1 text-xs text-slate-500">Selesai &middot; {{ $stats['ditolak'] }} ditolak</p>
        </div>
    </div>

    {{-- Stacked Composition Bar --}}
    <div class="mt-5 bg-white rounded-2xl border border-slate-200/90 p-5">
        <div class="flex flex-wrap items-center justify-between gap-2 text-xs mb-3">
            <span class="font-bold text-slate-900">Komposisi Status Laporan</span>
            <div class="flex flex-wrap items-center gap-4 text-[11px] text-slate-600">
                <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span>Menunggu ({{ $stats['menunggu'] }})</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-sky-500"></span>Diverifikasi ({{ $stats['diverifikasi'] }})</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-indigo-500"></span>Diproses ({{ $stats['diproses'] }})</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span>Selesai ({{ $stats['selesai'] }})</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Ditolak ({{ $stats['ditolak'] }})</span>
            </div>
        </div>
        <div class="w-full h-3 rounded-full bg-slate-100 overflow-hidden flex">
            <div class="bg-amber-500 h-full" style="width: {{ round(($stats['menunggu'] / $total) * 100) }}%"></div>
            <div class="bg-sky-500 h-full" style="width: {{ round(($stats['diverifikasi'] / $total) * 100) }}%"></div>
            <div class="bg-indigo-500 h-full" style="width: {{ round(($stats['diproses'] / $total) * 100) }}%"></div>
            <div class="bg-emerald-500 h-full" style="width: {{ round(($stats['selesai'] / $total) * 100) }}%"></div>
            <div class="bg-rose-500 h-full" style="width: {{ round(($stats['ditolak'] / $total) * 100) }}%"></div>
        </div>
    </div>

    {{-- Breakdown Grid --}}
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200/90 p-6">
            <h2 class="text-sm font-extrabold text-slate-950">Distribusi per Kategori Bidang</h2>
            <div class="mt-4 space-y-3.5">
                @php $maxCat = max(1, $categoryStats->max('reports_count')); @endphp
                @foreach ($categoryStats as $cat)
                    <div>
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="font-medium text-slate-700">{{ $cat->name }}</span>
                            <span class="font-mono font-bold text-slate-900 tabular-nums">{{ $cat->reports_count }}</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-red-700 rounded-full" style="width: {{ round(($cat->reports_count / $maxCat) * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/90 p-6">
            <h2 class="text-sm font-extrabold text-slate-950">Distribusi per Klasifikasi</h2>
            <div class="mt-4 space-y-4">
                @php $maxJenis = max(1, $jenisStats->max()); @endphp
                @foreach ($jenisStats as $jenis => $count)
                    <div>
                        <div class="flex justify-between text-xs mb-1.5">
                            <span class="font-medium text-slate-700">{{ $jenis }}</span>
                            <span class="font-mono font-bold text-slate-900 tabular-nums">{{ $count }}</span>
                        </div>
                        <div class="w-full h-2.5 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-slate-900 rounded-full" style="width: {{ round(($count / $maxJenis) * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent Reports --}}
    <div class="mt-6 bg-white rounded-2xl border border-slate-200/90 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-extrabold text-slate-950">Laporan Masuk Terbaru</h2>
                <p class="text-xs text-slate-500">Klik "Tindak Lanjut" untuk verifikasi, ubah status, atau balas pelapor.</p>
            </div>
            <a href="{{ route('admin.reports.index') }}" class="text-xs font-bold text-red-700 hover:underline">Kelola Semua &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200/80">
                    <tr>
                        <th class="px-6 py-3.5">Kode Tiket</th>
                        <th class="px-6 py-3.5">Judul &amp; Metadata</th>
                        <th class="px-6 py-3.5">Pelapor</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentReports as $item)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4 font-mono text-xs font-bold text-slate-800 whitespace-nowrap">{{ $item->ticket_code }}</td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-950 line-clamp-1">{{ $item->judul_laporan }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $item->jenis_laporan }} &middot; {{ $item->category?->name ?? '-' }} &middot; {{ $item->privacy_label }}</p>
                            </td>
                            <td class="px-6 py-4 text-xs whitespace-nowrap">
                                <p class="font-semibold text-slate-800">{{ $item->nama_pelapor }}</p>
                                <p class="font-mono text-slate-500">{{ $item->nim_nip }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $item->status_badge_class }}">{{ $item->status_label }}</span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.reports.show', $item) }}" class="btn-press inline-block px-3.5 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800">
                                    Tindak Lanjut
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">Belum ada laporan masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
