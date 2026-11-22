@extends('layouts.admin')

@section('title', 'Dashboard — Admin SILIRA')

@section('content')
    @php
        $total = max(1, $stats['total']);
        $completionRate = $stats['total'] > 0 ? round(($stats['selesai'] / $stats['total']) * 100) : 0;
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold tracking-[-0.02em] text-stone-950">Dashboard</h1>
            <p class="text-xs text-stone-500 mt-0.5">Ringkasan laporan masuk dan progres penanganan DPM FDIK.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reports.index', ['status' => 'menunggu']) }}" class="btn-press px-3.5 py-2 rounded-lg border border-stone-300 bg-white text-stone-800 text-xs font-medium hover:bg-stone-50">
                Antrean Verifikasi ({{ $stats['menunggu'] }})
            </a>
            <a href="{{ route('admin.reports.export') }}" class="btn-press px-3.5 py-2 rounded-lg bg-stone-900 text-white text-xs font-semibold hover:bg-stone-800">
                Export CSV
            </a>
        </div>
    </div>

    {{-- Unified Metric Strip --}}
    <div class="mt-6 bg-white rounded-xl border border-stone-200 grid grid-cols-2 lg:grid-cols-4 divide-y lg:divide-y-0 divide-x divide-stone-200 overflow-hidden">
        <div class="p-5">
            <p class="text-xs text-stone-500">Total Laporan</p>
            <p class="mt-1.5 text-2xl font-bold font-mono text-stone-950 tabular-nums">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="p-5">
            <p class="text-xs text-stone-500">Menunggu Verifikasi</p>
            <p class="mt-1.5 text-2xl font-bold font-mono text-amber-700 tabular-nums">{{ number_format($stats['menunggu']) }}</p>
        </div>
        <div class="p-5">
            <p class="text-xs text-stone-500">Diverifikasi / Diproses</p>
            <p class="mt-1.5 text-2xl font-bold font-mono text-stone-950 tabular-nums">{{ number_format($stats['diverifikasi'] + $stats['diproses']) }}</p>
        </div>
        <div class="p-5">
            <div class="flex items-center justify-between">
                <p class="text-xs text-stone-500">Selesai</p>
                <span class="font-mono text-[11px] font-semibold text-emerald-800">{{ $completionRate }}%</span>
            </div>
            <p class="mt-1.5 text-2xl font-bold font-mono text-emerald-800 tabular-nums">{{ number_format($stats['selesai']) }}</p>
        </div>
    </div>

    {{-- Distribution --}}
    <div class="mt-5 grid grid-cols-1 lg:grid-cols-12 gap-5">
        <div class="lg:col-span-7 bg-white rounded-xl border border-stone-200 p-5">
            <h2 class="text-xs font-semibold text-stone-950">Distribusi Kategori Bidang</h2>
            <div class="mt-4 space-y-3">
                @php $maxCat = max(1, $categoryStats->max('reports_count')); @endphp
                @foreach ($categoryStats as $cat)
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-stone-700">{{ $cat->name }}</span>
                            <span class="font-mono font-medium text-stone-900 tabular-nums">{{ $cat->reports_count }}</span>
                        </div>
                        <div class="w-full h-1.5 rounded-full bg-stone-100 overflow-hidden">
                            <div class="h-full bg-emerald-800 rounded-full" style="width: {{ round(($cat->reports_count / $maxCat) * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-5 bg-white rounded-xl border border-stone-200 p-5 flex flex-col justify-between">
            <div>
                <h2 class="text-xs font-semibold text-stone-950">Klasifikasi Laporan</h2>
                <div class="mt-4 space-y-3.5">
                    @php $maxJenis = max(1, $jenisStats->max()); @endphp
                    @foreach ($jenisStats as $jenis => $count)
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-stone-700">{{ $jenis }}</span>
                                <span class="font-mono font-medium text-stone-900 tabular-nums">{{ $count }}</span>
                            </div>
                            <div class="w-full h-1.5 rounded-full bg-stone-100 overflow-hidden">
                                <div class="h-full bg-stone-900 rounded-full" style="width: {{ round(($count / $maxJenis) * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-stone-100">
                <p class="text-[11px] font-medium text-stone-500 mb-2">Komposisi Status</p>
                <div class="w-full h-2 rounded-full bg-stone-100 overflow-hidden flex">
                    <div class="bg-amber-500 h-full" style="width: {{ round(($stats['menunggu'] / $total) * 100) }}%"></div>
                    <div class="bg-sky-500 h-full" style="width: {{ round(($stats['diverifikasi'] / $total) * 100) }}%"></div>
                    <div class="bg-indigo-500 h-full" style="width: {{ round(($stats['diproses'] / $total) * 100) }}%"></div>
                    <div class="bg-emerald-600 h-full" style="width: {{ round(($stats['selesai'] / $total) * 100) }}%"></div>
                    <div class="bg-rose-500 h-full" style="width: {{ round(($stats['ditolak'] / $total) * 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Reports --}}
    <div class="mt-5 bg-white rounded-xl border border-stone-200 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-stone-200 flex items-center justify-between">
            <h2 class="text-xs font-semibold text-stone-950">Laporan Terbaru</h2>
            <a href="{{ route('admin.reports.index') }}" class="text-xs font-medium text-emerald-900 hover:underline">Kelola semua &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-[11px] font-medium text-stone-500 border-b border-stone-200">
                    <tr>
                        <th class="px-5 py-2.5">Tiket</th>
                        <th class="px-5 py-2.5">Laporan</th>
                        <th class="px-5 py-2.5">Pelapor</th>
                        <th class="px-5 py-2.5">Status</th>
                        <th class="px-5 py-2.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($recentReports as $item)
                        <tr class="hover:bg-stone-50/70 transition">
                            <td class="px-5 py-3 font-mono font-medium text-stone-700 whitespace-nowrap">{{ $item->ticket_code }}</td>
                            <td class="px-5 py-3">
                                <p class="font-semibold text-stone-950 line-clamp-1">{{ $item->judul_laporan }}</p>
                                <p class="text-[11px] text-stone-500">{{ $item->jenis_laporan }} &middot; {{ $item->category?->name ?? '-' }} &middot; {{ $item->privacy_label }}</p>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <p class="font-medium text-stone-800">{{ $item->nama_pelapor }}</p>
                                <p class="font-mono text-[11px] text-stone-500">{{ $item->nim_nip }}</p>
                            </td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-md font-medium {{ $item->status_badge_class }}">{{ $item->status_label }}</span>
                            </td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.reports.show', $item) }}" class="btn-press inline-block px-3 py-1.5 rounded-md border border-stone-300 bg-white text-stone-800 font-medium hover:bg-stone-50">
                                    Buka
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-stone-500">Belum ada laporan masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
