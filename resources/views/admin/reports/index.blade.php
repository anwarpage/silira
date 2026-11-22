@extends('layouts.admin')

@section('title', 'Daftar Laporan — Admin SILIRA')

@section('content')
    @php
        $statusDots = [
            'menunggu' => 'bg-amber-500',
            'diverifikasi' => 'bg-sky-500',
            'diproses' => 'bg-indigo-500',
            'selesai' => 'bg-emerald-500',
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
            <span class="text-xs font-bold uppercase tracking-wider text-red-700">Arsip &amp; Tindak Lanjut</span>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950">Manajemen Laporan Masuk</h1>
            <p class="text-sm text-slate-500 mt-0.5">Filter, verifikasi, tanggapi, edit, atau ekspor laporan aspirasi mahasiswa.</p>
        </div>
        <a href="{{ route('admin.reports.export', request()->query()) }}" class="btn-press inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800">
            Export Filter Aktif (CSV)
        </a>
    </div>

    {{-- Well-made Custom Dropdown Filter Bar --}}
    <form method="GET" action="{{ route('admin.reports.index') }}" class="mt-6 bg-white rounded-2xl border border-slate-200/90 p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center shadow-2xs">
        <div class="lg:col-span-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode tiket, nama, NIM, atau judul..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300/90 text-xs focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600">
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
            <button type="submit" class="btn-press flex-1 px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 cursor-pointer">Filter</button>
            @if (request()->hasAny(['search', 'status', 'category_id', 'jenis', 'privacy', 'month', 'year']))
                <a href="{{ route('admin.reports.index') }}" title="Reset Filter" class="px-2.5 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs hover:bg-slate-100">&times;</a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="mt-6 bg-white rounded-2xl border border-slate-200/90 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200/80">
                    <tr>
                        <th class="px-5 py-3.5">Tiket &amp; Waktu</th>
                        <th class="px-5 py-3.5">Laporan</th>
                        <th class="px-5 py-3.5">Pelapor</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($reports as $report)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="font-mono text-xs font-bold text-slate-900 block">{{ $report->ticket_code }}</span>
                                <span class="text-[11px] text-slate-500">{{ $report->created_at->format('d M Y, H:i') }}</span>
                            </td>
                            <td class="px-5 py-4 max-w-md">
                                <p class="font-semibold text-slate-950 line-clamp-1">{{ $report->judul_laporan }}</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-medium">{{ $report->jenis_laporan }}</span>
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600">{{ $report->privacy_label }}</span>
                                    @if ($report->category)
                                        <span class="text-slate-500">{{ $report->category->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 text-xs whitespace-nowrap">
                                <p class="font-semibold text-slate-800">{{ $report->nama_pelapor }}</p>
                                <p class="font-mono text-slate-500">{{ $report->nim_nip }} &middot; {{ $report->no_wa }}</p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.reports.show', $report) }}" class="btn-press px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800">Detail</a>
                                    <a href="{{ route('admin.reports.edit', $report) }}" class="btn-press px-2.5 py-1.5 rounded-lg border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-100">Edit</a>
                                    <form action="{{ route('admin.reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Hapus laporan {{ $report->ticket_code }} secara permanen?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-press px-2.5 py-1.5 rounded-lg bg-rose-50 text-rose-700 text-xs font-semibold hover:bg-rose-100 cursor-pointer">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500">Tidak ada laporan yang cocok dengan filter aktif.</td>
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
