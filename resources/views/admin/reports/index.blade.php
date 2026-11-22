@extends('layouts.admin')

@section('title', 'Daftar Laporan — Admin SILIRA')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Manajemen Laporan Masuk</h1>
            <p class="text-sm text-slate-500 mt-0.5">Filter, verifikasi, tanggapi, edit, atau ekspor laporan aspirasi mahasiswa.</p>
        </div>
        <a href="{{ route('admin.reports.export', request()->query()) }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition">
            Export Filter Aktif (CSV)
        </a>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route('admin.reports.index') }}" class="mt-6 bg-white rounded-2xl border border-slate-200 p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama, NIM, judul..." class="lg:col-span-2 px-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-red-600">

        <select name="status" class="px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-red-600">
            <option value="">Semua Status</option>
            @foreach (\App\Models\Report::STATUSES as $k => $label)
                <option value="{{ $k }}" {{ request('status') === $k ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <select name="category_id" class="px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-red-600">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>

        <select name="month" class="px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-red-600">
            <option value="">Semua Bulan</option>
            @for ($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
            @endfor
        </select>

        <div class="flex gap-2">
            <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition cursor-pointer">Filter</button>
            @if (request()->hasAny(['search', 'status', 'category_id', 'jenis', 'privacy', 'month', 'year']))
                <a href="{{ route('admin.reports.index') }}" class="px-3 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs hover:bg-slate-100">Reset</a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="mt-6 bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3">Tiket &amp; Tanggal</th>
                        <th class="px-5 py-3">Laporan</th>
                        <th class="px-5 py-3">Pelapor</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($reports as $report)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="font-mono text-xs font-bold text-slate-900 block">{{ $report->ticket_code }}</span>
                                <span class="text-[11px] text-slate-500">{{ $report->created_at->format('d M Y, H:i') }}</span>
                            </td>
                            <td class="px-5 py-3.5 max-w-md">
                                <p class="font-semibold text-slate-900 line-clamp-1">{{ $report->judul_laporan }}</p>
                                <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium">{{ $report->jenis_laporan }}</span>
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600">{{ $report->privacy_label }}</span>
                                    @if ($report->category)
                                        <span class="text-slate-500">{{ $report->category->name }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-xs whitespace-nowrap">
                                <p class="font-semibold text-slate-800">{{ $report->nama_pelapor }}</p>
                                <p class="text-slate-500">{{ $report->nim_nip }} &middot; {{ $report->no_wa }}</p>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('admin.reports.show', $report) }}" class="px-2.5 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800">Detail</a>
                                    <a href="{{ route('admin.reports.edit', $report) }}" class="px-2.5 py-1.5 rounded-lg border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-100">Edit</a>
                                    <form action="{{ route('admin.reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Hapus laporan {{ $report->ticket_code }} secara permanen?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-50 text-rose-700 text-xs font-semibold hover:bg-rose-100 cursor-pointer">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-slate-500">Tidak ada laporan yang ditemukan.</td>
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
