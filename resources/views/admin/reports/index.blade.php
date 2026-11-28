@extends('layouts.admin')

@section('title', 'Laporan Masuk — SILIRA')

@section('content')
    @php
        $statusDots = [
            'menunggu' => 'bg-[#d97706]',
            'diverifikasi' => 'bg-[#0284c7]',
            'diproses' => 'bg-[#1e3a8a]',
            'selesai' => 'bg-[#16a34a]',
            'ditolak' => 'bg-[#b91c1c]',
        ];
        $statusOptions = collect([['value' => '', 'label' => 'Semua status']])
            ->merge(collect(\App\Models\Report::STATUSES)->map(fn ($label, $key) => [
                'value' => $key,
                'label' => $label,
                'dot' => $statusDots[$key] ?? null,
            ]))
            ->all();

        $categoryOptions = collect([['value' => '', 'label' => 'Semua kategori']])
            ->merge($categories->map(fn ($c) => ['value' => $c->id, 'label' => $c->name]))
            ->all();

        $monthOptions = collect([['value' => '', 'label' => 'Semua bulan']])
            ->merge(collect(range(1, 12))->map(fn ($m) => [
                'value' => $m,
                'label' => \Carbon\Carbon::create()->month($m)->translatedFormat('F'),
            ]))
            ->all();

        $yearOptions = collect([['value' => '', 'label' => 'Semua tahun']])
            ->merge($availableYears->map(fn ($y) => [
                'value' => $y,
                'label' => (string) $y,
            ]))
            ->all();
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-6 border-b border-[#e5e7eb]">
        <div>
            <h1 class="text-[30px] font-semibold text-[#0f172a]">Laporan Masuk</h1>
            <p class="text-[15px] text-[#475569] mt-1">Kelola, saring, tindak lanjuti, atau ekspor laporan civitas FDIK.</p>
        </div>
        <a href="{{ route('admin.reports.export', request()->query()) }}" class="s-btn-primary">
            Ekspor Filter Aktif (CSV)
        </a>
    </div>

    <form method="GET" action="{{ route('admin.reports.index') }}" class="mt-6 s-card p-4 flex flex-col gap-3">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 min-w-0">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor tiket, nama pelapor, NIM, atau judul laporan..." class="s-input s-input-sm">
            </div>
            <div class="hidden sm:flex items-center gap-2 shrink-0">
                <button type="submit" class="s-btn-primary h-[40px] px-5 text-[14px]">Terapkan Filter</button>
                @if (request()->hasAny(['search', 'status', 'category_id', 'jenis', 'privacy', 'month', 'year']))
                    <a href="{{ route('admin.reports.index') }}" class="s-btn-secondary h-[40px] px-3.5 text-[14px]">Reset</a>
                @endif
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <x-select-dropdown name="status" :options="$statusOptions" :selected="request('status')" placeholder="Semua status" size="sm" />
            </div>
            <div>
                <x-select-dropdown name="category_id" :options="$categoryOptions" :selected="request('category_id')" placeholder="Semua kategori" size="sm" :searchable="true" />
            </div>
            <div>
                <x-select-dropdown name="month" :options="$monthOptions" :selected="request('month')" placeholder="Semua bulan" size="sm" />
            </div>
            <div>
                <x-select-dropdown name="year" :options="$yearOptions" :selected="request('year')" placeholder="Semua tahun" size="sm" />
            </div>
        </div>
        <div class="flex sm:hidden items-center gap-2 pt-1">
            <button type="submit" class="s-btn-primary flex-1 h-[40px] px-5 text-[14px]">Terapkan Filter</button>
            @if (request()->hasAny(['search', 'status', 'category_id', 'jenis', 'privacy', 'month', 'year']))
                <a href="{{ route('admin.reports.index') }}" class="s-btn-secondary h-[40px] px-4 text-[14px]">Reset</a>
            @endif
        </div>
    </form>

    <div class="mt-6 s-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[14px]">
                <thead class="bg-[#f8fafc] text-[12.5px] font-medium text-[#475569] border-b border-[#e5e7eb]">
                    <tr>
                        <th class="px-4 py-3">Tiket &amp; Waktu</th>
                        <th class="px-4 py-3">Laporan</th>
                        <th class="px-4 py-3">Pelapor</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e5e7eb]">
                    @forelse ($reports as $report)
                        <tr class="hover:bg-[#f8fafc] transition">
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="font-mono text-[12.5px] font-medium text-[#0f172a] block">{{ $report->ticket_code }}</span>
                                <span class="text-[12px] text-[#475569]">{{ $report->created_at->format('d M Y, H:i') }}</span>
                            </td>
                            <td class="px-4 py-3.5 max-w-[280px]">
                                <p class="font-medium text-[#0f172a] line-clamp-1">{{ $report->judul_laporan }}</p>
                                <p class="text-[12.5px] text-[#475569] line-clamp-1">{{ $report->jenis_laporan }} &middot; {{ $report->privacy_label }} @if ($report->category) &middot; {{ $report->category->name }} @endif</p>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <p class="text-[13.5px] text-[#0f172a]">{{ $report->nama_pelapor }}</p>
                                <p class="font-mono text-[12px] text-[#475569]">{{ $report->nim_nip }}</p>
                            </td>
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-[6px] text-[12px] font-medium {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
                            </td>
                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.reports.show', $report) }}" class="s-btn-primary h-[34px] px-3 text-[12.5px]">Detail</a>
                                    <a href="{{ route('admin.reports.edit', $report) }}" class="s-btn-secondary h-[34px] px-2.5 text-[12.5px]">Edit</a>
                                    <form action="{{ route('admin.reports.destroy', $report) }}" method="POST" data-confirm-title="Hapus laporan {{ $report->ticket_code }}?" data-confirm-message="Laporan beserta seluruh riwayat tanggapan dan lampirannya akan dihapus secara permanen dan tidak dapat dipulihkan." class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1.5 rounded-[9px] text-[12.5px] font-medium text-[#991b1b] hover:bg-[#fee2e2] cursor-pointer">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-[#475569]">Tidak ada laporan yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $reports->links() }}
    </div>
@endsection
