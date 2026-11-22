@extends('layouts.admin')

@section('title', 'Dashboard — Silira')

@section('content')
    @php
        $total = max(1, $stats['total']);
        $completionRate = $stats['total'] > 0 ? round(($stats['selesai'] / $stats['total']) * 100) : 0;
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-[28px] font-medium text-[#0f172a]">Dashboard</h1>
            <p class="text-[15px] text-[#475569] mt-1">Ringkasan aspirasi masuk dan progres penanganan DPM FDIK.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.reports.index', ['status' => 'menunggu']) }}" class="s-btn-secondary whitespace-nowrap">
                Antrean verifikasi ({{ $stats['menunggu'] }})
            </a>
            <a href="{{ route('admin.reports.export') }}" class="s-btn-primary whitespace-nowrap">
                Export CSV
            </a>
        </div>
    </div>

    {{-- Unified Metric Strip --}}
    <div class="mt-7 s-card grid grid-cols-2 lg:grid-cols-4 divide-y lg:divide-y-0 divide-x divide-[#0f172a]/12 overflow-hidden">
        <div class="p-6">
            <p class="text-[13px] font-medium text-[#475569]">Total laporan</p>
            <p class="mt-2 text-[32px] font-medium font-mono text-[#0f172a] tabular-nums">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="p-6">
            <p class="text-[13px] font-medium text-[#475569]">Menunggu verifikasi</p>
            <p class="mt-2 text-[32px] font-medium font-mono text-[#92400e] tabular-nums">{{ number_format($stats['menunggu']) }}</p>
        </div>
        <div class="p-6">
            <p class="text-[13px] font-medium text-[#475569]">Diverifikasi &amp; diproses</p>
            <p class="mt-2 text-[32px] font-medium font-mono text-[#0f172a] tabular-nums">{{ number_format($stats['diverifikasi'] + $stats['diproses']) }}</p>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-between">
                <p class="text-[13px] font-medium text-[#475569]">Selesai</p>
                <span class="font-mono text-[13px] font-medium text-[#1e3a8a]">{{ $completionRate }}%</span>
            </div>
            <p class="mt-2 text-[32px] font-medium font-mono text-[#1e3a8a] tabular-nums">{{ number_format($stats['selesai']) }}</p>
        </div>
    </div>

    {{-- Distribution --}}
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-7 s-card p-6">
            <h2 class="text-[16px] font-medium text-[#0f172a]">Distribusi kategori bidang</h2>
            <div class="mt-5 space-y-4">
                @php $maxCat = max(1, $categoryStats->max('reports_count')); @endphp
                @foreach ($categoryStats as $cat)
                    <div>
                        <div class="flex justify-between text-[14px] mb-1.5">
                            <span class="text-[#0f172a]">{{ $cat->name }}</span>
                            <span class="font-mono text-[#475569] tabular-nums">{{ $cat->reports_count }}</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-[#f5f7fa] overflow-hidden">
                            <div class="h-full bg-[#1e3a8a] rounded-full" style="width: {{ round(($cat->reports_count / $maxCat) * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-5 s-card p-6 flex flex-col justify-between">
            <div>
                <h2 class="text-[16px] font-medium text-[#0f172a]">Klasifikasi laporan</h2>
                <div class="mt-5 space-y-4">
                    @php $maxJenis = max(1, $jenisStats->max()); @endphp
                    @foreach ($jenisStats as $jenis => $count)
                        <div>
                            <div class="flex justify-between text-[14px] mb-1.5">
                                <span class="text-[#0f172a]">{{ $jenis }}</span>
                                <span class="font-mono text-[#475569] tabular-nums">{{ $count }}</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-[#f5f7fa] overflow-hidden">
                                <div class="h-full bg-[#0f172a] rounded-full" style="width: {{ round(($count / $maxJenis) * 100) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 pt-5 border-t border-[#0f172a]/12">
                <p class="text-[13px] font-medium text-[#475569] mb-2.5">Komposisi status</p>
                <div class="w-full h-2.5 rounded-full bg-[#f5f7fa] overflow-hidden flex">
                    <div class="bg-[#d97706] h-full" style="width: {{ round(($stats['menunggu'] / $total) * 100) }}%"></div>
                    <div class="bg-[#0284c7] h-full" style="width: {{ round(($stats['diverifikasi'] / $total) * 100) }}%"></div>
                    <div class="bg-[#1e3a8a] h-full" style="width: {{ round(($stats['diproses'] / $total) * 100) }}%"></div>
                    <div class="bg-[#1e3a8a] h-full" style="width: {{ round(($stats['selesai'] / $total) * 100) }}%"></div>
                    <div class="bg-[#dc2626] h-full" style="width: {{ round(($stats['ditolak'] / $total) * 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Reports --}}
    <div class="mt-6 s-card overflow-hidden">
        <div class="px-6 py-4 border-b border-[#0f172a]/12 flex items-center justify-between">
            <h2 class="text-[16px] font-medium text-[#0f172a]">Laporan terbaru</h2>
            <a href="{{ route('admin.reports.index') }}" class="text-[14px] font-medium text-[#1e3a8a] hover:underline">Kelola semua &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-[14px]">
                <thead class="bg-[#f5f7fa]/60 text-[13px] font-medium text-[#475569] border-b border-[#0f172a]/12">
                    <tr>
                        <th class="px-6 py-3">Tiket</th>
                        <th class="px-6 py-3">Laporan</th>
                        <th class="px-6 py-3">Pelapor</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#0f172a]/10">
                    @forelse ($recentReports as $item)
                        <tr class="hover:bg-[#f5f7fa]/40 transition">
                            <td class="px-6 py-4 font-mono text-[13px] text-[#475569] whitespace-nowrap">{{ $item->ticket_code }}</td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-[#0f172a] line-clamp-1">{{ $item->judul_laporan }}</p>
                                <p class="text-[13px] text-[#475569]">{{ $item->jenis_laporan }} &middot; {{ $item->category?->name ?? '-' }} &middot; {{ $item->privacy_label }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-[#0f172a]">{{ $item->nama_pelapor }}</p>
                                <p class="font-mono text-[13px] text-[#475569]">{{ $item->nim_nip }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-[6px] text-[13px] font-medium {{ $item->status_badge_class }}">{{ $item->status_label }}</span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.reports.show', $item) }}" class="s-btn-secondary h-[36px] px-3.5 text-[13px]">
                                    Buka
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-[#475569]">Belum ada laporan masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
