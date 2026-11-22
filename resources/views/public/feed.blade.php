@extends('layouts.public')

@section('title', 'Aspirasi Publik — SILIRA DPM FDIK')

@section('content')
    @php
        $categoryOptions = collect([['value' => '', 'label' => 'Semua Kategori']])
            ->merge($categories->map(fn ($c) => ['value' => $c->id, 'label' => $c->name]))
            ->all();

        $jenisOptions = collect([['value' => '', 'label' => 'Semua Klasifikasi']])
            ->merge(collect(\App\Models\Report::JENIS_OPTIONS)->map(fn ($j) => ['value' => $j, 'label' => $j]))
            ->all();

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
    @endphp

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-red-700">Transparansi Fakultas</span>
                <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-950">Aspirasi &amp; Pengaduan Publik</h1>
                <p class="mt-1.5 text-sm text-slate-600">Pantau aspirasi yang sedang dikawal DPM FDIK. Laporan berstatus Rahasia tidak dipublikasikan.</p>
            </div>
            <a href="{{ route('reports.create') }}" class="btn-press inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-red-700 text-white font-bold text-sm hover:bg-red-800 shadow-2xs">
                + Buat Laporan
            </a>
        </div>

        {{-- Custom Filter Panel --}}
        <form method="GET" action="{{ route('public.feed') }}" class="mt-7 bg-white rounded-2xl border border-slate-200/90 p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center shadow-2xs">
            <div class="lg:col-span-4">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul, isi, atau kode tiket..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300/90 text-xs focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600">
            </div>

            <div class="lg:col-span-3">
                <x-select-dropdown name="category_id" :options="$categoryOptions" :selected="request('category_id')" placeholder="Semua Kategori" size="sm" :searchable="true" />
            </div>

            <div class="lg:col-span-2">
                <x-select-dropdown name="jenis" :options="$jenisOptions" :selected="request('jenis')" placeholder="Semua Klasifikasi" size="sm" />
            </div>

            <div class="lg:col-span-2">
                <x-select-dropdown name="status" :options="$statusOptions" :selected="request('status')" placeholder="Semua Status" size="sm" />
            </div>

            <div class="lg:col-span-1 flex gap-1.5">
                <button type="submit" class="btn-press flex-1 px-3 py-2 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 cursor-pointer">Cari</button>
                @if (request()->hasAny(['q', 'category_id', 'jenis', 'status']))
                    <a href="{{ route('public.feed') }}" title="Reset Filter" class="px-2.5 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs hover:bg-slate-100">&times;</a>
                @endif
            </div>
        </form>

        {{-- List --}}
        <div class="mt-6 space-y-4">
            @forelse ($reports as $item)
                <article class="card-interactive bg-white rounded-2xl border border-slate-200/90 p-6 hover:border-slate-300 hover:shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="font-mono font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-md">{{ $item->ticket_code }}</span>
                            <span class="px-2.5 py-0.5 rounded-full font-semibold {{ $item->status_badge_class }}">{{ $item->status_label }}</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 font-medium">{{ $item->jenis_laporan }}</span>
                            @if ($item->category)
                                <span class="text-slate-500 font-medium">{{ $item->category->name }}</span>
                            @endif
                        </div>
                        <span class="text-xs text-slate-500">{{ $item->created_at->format('d M Y, H:i') }} WIB</span>
                    </div>

                    <h2 class="mt-3.5 text-lg font-bold text-slate-950 leading-snug">
                        <a href="{{ route('tracking.show', $item->ticket_code) }}" class="hover:text-red-700 transition">
                            {{ $item->judul_laporan }}
                        </a>
                    </h2>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ \Illuminate\Support\Str::limit($item->isi_laporan, 240) }}</p>

                    <div class="mt-4 pt-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Pelapor: <strong class="text-slate-800">{{ $item->public_reporter_name }}</strong></span>
                        <a href="{{ route('tracking.show', $item->ticket_code) }}" class="font-bold text-red-700 hover:underline">
                            Lihat Timeline &amp; {{ $item->public_responses_count }} Progres &rarr;
                        </a>
                    </div>
                </article>
            @empty
                <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center text-sm text-slate-500">
                    Tidak ada laporan publik yang cocok dengan filter pencarian Anda.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $reports->links() }}
        </div>
    </div>
@endsection
