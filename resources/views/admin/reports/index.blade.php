@extends('layouts.admin')

@section('title', 'Laporan masuk — Silira')

@section('content')
    @php
        $statusDots = [
            'menunggu' => 'bg-[#b78328]',
            'diverifikasi' => 'bg-[#1e4b63]',
            'diproses' => 'bg-[#3a3469]',
            'selesai' => 'bg-[#456f3a]',
            'ditolak' => 'bg-[#9b3a34]',
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
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-[28px] font-medium text-[#2a1d17]">Laporan masuk</h1>
            <p class="text-[15px] text-[#65564c] mt-1">Kelola, filter, tindak lanjuti, atau ekspor laporan mahasiswa.</p>
        </div>
        <a href="{{ route('admin.reports.export', request()->query()) }}" class="s-btn-primary">
            Export filter aktif (CSV)
        </a>
    </div>

    <form method="GET" action="{{ route('admin.reports.index') }}" class="mt-6 s-card p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
        <div class="lg:col-span-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama, NIM, atau judul..." class="s-input s-input-sm">
        </div>
        <div class="lg:col-span-2">
            <x-select-dropdown name="status" :options="$statusOptions" :selected="request('status')" placeholder="Semua status" size="sm" />
        </div>
        <div class="lg:col-span-3">
            <x-select-dropdown name="category_id" :options="$categoryOptions" :selected="request('category_id')" placeholder="Semua kategori" size="sm" :searchable="true" />
        </div>
        <div class="lg:col-span-2">
            <x-select-dropdown name="month" :options="$monthOptions" :selected="request('month')" placeholder="Semua bulan" size="sm" />
        </div>
        <div class="lg:col-span-1 flex gap-1.5">
            <button type="submit" class="s-btn-primary flex-1 h-[42px] px-3 text-[14px]">Filter</button>
            @if (request()->hasAny(['search', 'status', 'category_id', 'jenis', 'privacy', 'month', 'year']))
                <a href="{{ route('admin.reports.index') }}" title="Reset" class="s-btn-secondary h-[42px] px-3">&times;</a>
            @endif
        </div>
    </form>

    <div class="mt-6 s-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-[14px]">
                <thead class="bg-[#e5e1d8]/60 text-[13px] font-medium text-[#65564c] border-b border-[#2a1d17]/12">
                    <tr>
                        <th class="px-5 py-3">Tiket &amp; waktu</th>
                        <th class="px-5 py-3">Laporan</th>
                        <th class="px-5 py-3">Pelapor</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2a1d17]/10">
                    @forelse ($reports as $report)
                        <tr class="hover:bg-[#e5e1d8]/40 transition">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="font-mono text-[13px] font-medium text-[#2a1d17] block">{{ $report->ticket_code }}</span>
                                <span class="text-[13px] text-[#65564c]">{{ $report->created_at->format('d M Y, H:i') }}</span>
                            </td>
                            <td class="px-5 py-4 max-w-md">
                                <p class="font-medium text-[#2a1d17] line-clamp-1">{{ $report->judul_laporan }}</p>
                                <p class="text-[13px] text-[#65564c]">{{ $report->jenis_laporan }} &middot; {{ $report->privacy_label }} @if ($report->category) &middot; {{ $report->category->name }} @endif</p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="text-[#2a1d17]">{{ $report->nama_pelapor }}</p>
                                <p class="font-mono text-[13px] text-[#65564c]">{{ $report->nim_nip }}</p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-[6px] text-[13px] font-medium {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.reports.show', $report) }}" class="s-btn-primary h-[36px] px-3.5 text-[13px]">Detail</a>
                                    <a href="{{ route('admin.reports.edit', $report) }}" class="s-btn-secondary h-[36px] px-3 text-[13px]">Edit</a>
                                    <form action="{{ route('admin.reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Hapus laporan {{ $report->ticket_code }} secara permanen?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 rounded-[6px] text-[13px] font-medium text-[#7a2824] hover:bg-[#eedcdb] cursor-pointer">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-[#65564c]">Tidak ada laporan yang ditemukan.</td>
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
