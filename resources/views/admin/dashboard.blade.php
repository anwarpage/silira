@extends('layouts.admin')

@section('title', 'Dashboard Admin — SILIRA')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Dashboard Admin DPM</h1>
            <p class="text-sm text-slate-500 mt-0.5">Ringkasan aspirasi, pengaduan, dan progres tindak lanjut Fakultas Dakwah &amp; Ilmu Komunikasi.</p>
        </div>
        <a href="{{ route('admin.reports.export') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition">
            Export Semua (CSV/Excel)
        </a>
    </div>

    {{-- KPI Grid --}}
    <div class="mt-6 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach ([
            ['Total', $stats['total'], 'text-slate-900'],
            ['Menunggu', $stats['menunggu'], 'text-amber-700'],
            ['Diverifikasi', $stats['diverifikasi'], 'text-sky-700'],
            ['Diproses', $stats['diproses'], 'text-indigo-700'],
            ['Selesai', $stats['selesai'], 'text-emerald-700'],
            ['Ditolak', $stats['ditolak'], 'text-rose-700'],
        ] as [$label, $count, $color])
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <p class="text-xs font-semibold text-slate-500">{{ $label }}</p>
                <p class="mt-1.5 text-2xl font-extrabold tabular-nums {{ $color }}">{{ number_format($count) }}</p>
            </div>
        @endforeach
    </div>

    {{-- Breakdown --}}
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h2 class="text-sm font-bold text-slate-900">Distribusi per Kategori Bidang</h2>
            <div class="mt-4 space-y-3">
                @php $maxCat = max(1, $categoryStats->max('reports_count')); @endphp
                @foreach ($categoryStats as $cat)
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-slate-700">{{ $cat->name }}</span>
                            <span class="font-bold text-slate-900 tabular-nums">{{ $cat->reports_count }}</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-red-700 rounded-full" style="width: {{ round(($cat->reports_count / $maxCat) * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h2 class="text-sm font-bold text-slate-900">Distribusi per Klasifikasi</h2>
            <div class="mt-4 space-y-4">
                @php $maxJenis = max(1, $jenisStats->max()); @endphp
                @foreach ($jenisStats as $jenis => $total)
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="font-medium text-slate-700">{{ $jenis }}</span>
                            <span class="font-bold text-slate-900 tabular-nums">{{ $total }}</span>
                        </div>
                        <div class="w-full h-2.5 rounded-full bg-slate-100 overflow-hidden">
                            <div class="h-full bg-slate-900 rounded-full" style="width: {{ round(($total / $maxJenis) * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent Reports --}}
    <div class="mt-6 bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Laporan Masuk Terbaru</h2>
            <a href="{{ route('admin.reports.index') }}" class="text-xs font-semibold text-red-700 hover:underline">Kelola Semua &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3">Kode Tiket</th>
                        <th class="px-6 py-3">Judul &amp; Kategori</th>
                        <th class="px-6 py-3">Pelapor</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentReports as $item)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-6 py-3.5 font-mono text-xs font-bold text-slate-700 whitespace-nowrap">{{ $item->ticket_code }}</td>
                            <td class="px-6 py-3.5">
                                <p class="font-semibold text-slate-900 line-clamp-1">{{ $item->judul_laporan }}</p>
                                <p class="text-xs text-slate-500">{{ $item->jenis_laporan }} &middot; {{ $item->category?->name ?? '-' }} &middot; {{ $item->privacy_label }}</p>
                            </td>
                            <td class="px-6 py-3.5 text-xs">
                                <p class="font-semibold text-slate-800">{{ $item->nama_pelapor }}</p>
                                <p class="text-slate-500">{{ $item->nim_nip }}</p>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $item->status_badge_class }}">{{ $item->status_label }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                <a href="{{ route('admin.reports.show', $item) }}" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition">
                                    Tindak Lanjut
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">Belum ada laporan masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
