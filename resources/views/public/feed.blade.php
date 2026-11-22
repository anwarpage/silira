@extends('layouts.public')

@section('title', 'Aspirasi Publik — SILIRA DPM FDIK')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-red-700">Transparansi Fakultas</span>
                <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold text-slate-900">Daftar Aspirasi &amp; Pengaduan Publik</h1>
                <p class="mt-1 text-sm text-slate-600">Hanya menampilkan laporan berstatus Publik dan Anonim. Laporan Rahasia tidak dipublikasikan.</p>
            </div>
            <a href="{{ route('reports.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-red-700 text-white font-bold text-sm hover:bg-red-800 transition">
                + Buat Laporan
            </a>
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('public.feed') }}" class="mt-6 bg-white rounded-2xl border border-slate-200 p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul, isi, atau kode..." class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-600">

            <select name="category_id" class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            <select name="jenis" class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                <option value="">Semua Klasifikasi</option>
                @foreach (\App\Models\Report::JENIS_OPTIONS as $j)
                    <option value="{{ $j }}" {{ request('jenis') === $j ? 'selected' : '' }}>{{ $j }}</option>
                @endforeach
            </select>

            <select name="status" class="px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                <option value="">Semua Status</option>
                @foreach (\App\Models\Report::STATUSES as $k => $label)
                    <option value="{{ $k }}" {{ request('status') === $k ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition cursor-pointer">Filter</button>
                @if (request()->hasAny(['q', 'category_id', 'jenis', 'status']))
                    <a href="{{ route('public.feed') }}" class="px-3 py-2 rounded-xl border border-slate-300 text-slate-600 text-sm hover:bg-slate-100">Reset</a>
                @endif
            </div>
        </form>

        {{-- List --}}
        <div class="mt-6 space-y-4">
            @forelse ($reports as $item)
                <article class="bg-white rounded-2xl border border-slate-200 p-6 hover:border-slate-300 transition">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="font-mono font-bold text-slate-500">{{ $item->ticket_code }}</span>
                            <span class="px-2.5 py-0.5 rounded-full font-semibold {{ $item->status_badge_class }}">{{ $item->status_label }}</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-medium">{{ $item->jenis_laporan }}</span>
                            @if ($item->category)
                                <span class="text-slate-500">{{ $item->category->name }}</span>
                            @endif
                        </div>
                        <span class="text-xs text-slate-500">{{ $item->created_at->format('d M Y, H:i') }}</span>
                    </div>

                    <h2 class="mt-3 text-lg font-bold text-slate-900">
                        <a href="{{ route('tracking.show', $item->ticket_code) }}" class="hover:text-red-700 transition">
                            {{ $item->judul_laporan }}
                        </a>
                    </h2>
                    <p class="mt-1.5 text-sm text-slate-600 leading-relaxed">{{ \Illuminate\Support\Str::limit($item->isi_laporan, 240) }}</p>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Pelapor: <strong class="text-slate-700">{{ $item->public_reporter_name }}</strong></span>
                        <a href="{{ route('tracking.show', $item->ticket_code) }}" class="font-semibold text-red-700 hover:underline">
                            Lihat Detail &amp; {{ $item->public_responses_count }} Progres &rarr;
                        </a>
                    </div>
                </article>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center text-sm text-slate-500">
                    Tidak ada laporan publik yang cocok dengan filter pencarian.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $reports->links() }}
        </div>
    </div>
@endsection
