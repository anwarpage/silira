@extends('layouts.admin')

@section('title', 'Laporan Masuk — Admin SILIRA')

@section('content')
    @php
        $statusDots = [
            'menunggu' => 'bg-amber-500',
            'diverifikasi' => 'bg-sky-500',
            'diproses' => 'bg-indigo-500',
            'selesai' => 'bg-emerald-600',
            'ditolak' => 'bg-rose-500',
        ];
        $statusOptions = collect([['value' => '', 'label' => 'Semua Status']])
            ->merge(collect(\App\Models\Report::STATUSES)->map(fn ($label, $key) => [
                'value' => $key,
                'label' => $label,
                'dot' => $statusDots[$key] ?? null,
            ]))
            ->all();

        $categoryOptions = collect([['value' => '', 'label' => 'Semua Kategori']])
            ->merge($categories->map(fn ($c) => ['value' => $c->id, 'label' => $c->name]))
            ->all();

        $monthOptions = collect([['value' => '', 'label' => 'Semua Bulan']])
            ->merge(collect(range(1, 12))->map(fn ($m) => [
                'value' => $m,
                'label' => \Carbon\Carbon::create()->month($m)->translatedFormat('F'),
            ]))
            ->all();
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold tracking-[-0.02em] text-stone-950">Laporan Masuk</h1>
            <p class="text-xs text-stone-500 mt-0.5">Kelola, filter, tindak lanjuti, atau ekspor laporan mahasiswa.</p>
        </div>
        <a href="{{ route('admin.reports.export', request()->query()) }}" class="btn-press px-3.5 py-2 rounded-lg bg-emerald-900 text-white text-xs font-semibold hover:bg-emerald-950 text-center">
            Export Filter Aktif (CSV)
        </a>
    </div>

    <form method="GET" action="{{ route('admin.reports.index') }}" class="mt-5 bg-white rounded-xl border border-stone-200 p-3.5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2.5 items-center">
        <div class="lg:col-span-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama, NIM, atau judul..." class="w-full h-9 px-3 rounded-lg border border-stone-300 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-900/15 focus:border-emerald-800">
        </div>
        <div class="lg:col-span-2">
            <x-select-dropdown name="status" :options="$statusOptions" :selected="request('status')" placeholder="Semua Status" size="sm" />
        </div>
        <div class="lg:col-span-3">
            <x-select-dropdown name="category_id" :options="$categoryOptions" :selected="request('category_id')" placeholder="Semua Kategori" size="sm" :searchable="true" />
        </div>
        <div class="lg:col-span-2">
            <x-select-dropdown name="month" :options="$monthOptions" :selected="request('month')" placeholder="Semua Bulan" size="sm" />
        </div>
        <div class="lg:col-span-1 flex gap-1.5">
            <button type="submit" class="btn-press flex-1 h-9 px-3 rounded-lg bg-stone-900 text-white text-xs font-medium hover:bg-stone-800 cursor-pointer">Filter</button>
            @if (request()->hasAny(['search', 'status', 'category_id', 'jenis', 'privacy', 'month', 'year']))
                <a href="{{ route('admin.reports.index') }}" title="Reset" class="h-9 px-2.5 inline-flex items-center rounded-lg border border-stone-300 text-stone-600 text-xs hover:bg-stone-50">&times;</a>
            @endif
        </div>
    </form>

    <div class="mt-5 bg-white rounded-xl border border-stone-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-[11px] font-medium text-stone-500 border-b border-stone-200">
                    <tr>
                        <th class="px-5 py-2.5">Tiket &amp; Waktu</th>
                        <th class="px-5 py-2.5">Laporan</th>
                        <th class="px-5 py-2.5">Pelapor</th>
                        <th class="px-5 py-2.5">Status</th>
                        <th class="px-5 py-2.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($reports as $report)
                        <tr class="hover:bg-stone-50/70 transition">
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="font-mono font-semibold text-stone-900 block">{{ $report->ticket_code }}</span>
                                <span class="text-[11px] text-stone-500">{{ $report->created_at->format('d M Y, H:i') }}</span>
                            </td>
                            <td class="px-5 py-3.5 max-w-md">
                                <p class="font-semibold text-stone-950 line-clamp-1">{{ $report->judul_laporan }}</p>
                                <p class="text-[11px] text-stone-500 mt-0.5">{{ $report->jenis_laporan }} &middot; {{ $report->privacy_label }} @if ($report->category) &middot; {{ $report->category->name }} @endif</p>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <p class="font-medium text-stone-800">{{ $report->nama_pelapor }}</p>
                                <p class="font-mono text-[11px] text-stone-500">{{ $report->nim_nip }} &middot; {{ $report->no_wa }}</p>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-md font-medium {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.reports.show', $report) }}" class="btn-press px-2.5 py-1.5 rounded-md bg-stone-900 text-white font-medium hover:bg-stone-800">Detail</a>
                                    <a href="{{ route('admin.reports.edit', $report) }}" class="btn-press px-2.5 py-1.5 rounded-md border border-stone-300 bg-white text-stone-700 font-medium hover:bg-stone-50">Edit</a>
                                    <form action="{{ route('admin.reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Hapus laporan {{ $report->ticket_code }} secara permanen?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-press px-2.5 py-1.5 rounded-md text-rose-700 hover:bg-rose-50 font-medium cursor-pointer">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-stone-500">Tidak ada laporan yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        {{ $reports->links() }}
    </div>
@endsection
