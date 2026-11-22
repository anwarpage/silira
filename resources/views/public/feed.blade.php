@extends('layouts.public')

@section('title', 'Aspirasi Publik — SILIRA')

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
    @endphp

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-10">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-[-0.02em] text-stone-950">Aspirasi &amp; Pengaduan Publik</h1>
                <p class="mt-1 text-xs text-stone-600">Menampilkan laporan Publik dan Anonim. Laporan Rahasia tidak dipublikasikan.</p>
            </div>
            <a href="{{ route('reports.create') }}" class="btn-press px-4 py-2 rounded-lg bg-emerald-900 text-white text-xs font-semibold hover:bg-emerald-950 text-center">
                Kirim Aspirasi
            </a>
        </div>

        <form method="GET" action="{{ route('public.feed') }}" class="mt-6 bg-white rounded-xl border border-stone-200 p-3.5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-2.5 items-center">
            <div class="lg:col-span-4">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul, isi, atau kode tiket..." class="w-full h-9 px-3 rounded-lg border border-stone-300 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-900/15 focus:border-emerald-800">
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
                <button type="submit" class="btn-press flex-1 h-9 px-3 rounded-lg bg-stone-900 text-white text-xs font-medium hover:bg-stone-800 cursor-pointer">Cari</button>
                @if (request()->hasAny(['q', 'category_id', 'jenis', 'status']))
                    <a href="{{ route('public.feed') }}" title="Reset" class="h-9 px-2.5 inline-flex items-center rounded-lg border border-stone-300 text-stone-600 text-xs hover:bg-stone-50">&times;</a>
                @endif
            </div>
        </form>

        <div class="mt-5 bg-white rounded-xl border border-stone-200 divide-y divide-stone-200 overflow-hidden">
            @forelse ($reports as $item)
                <a href="{{ route('tracking.show', $item->ticket_code) }}" class="block px-5 py-4 hover:bg-stone-50/80 transition">
                    <div class="flex flex-wrap items-center justify-between gap-2 text-[11px]">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono font-medium text-stone-500">{{ $item->ticket_code }}</span>
                            <span class="px-2 py-0.5 rounded-md font-medium {{ $item->status_badge_class }}">{{ $item->status_label }}</span>
                            <span class="text-stone-500">{{ $item->jenis_laporan }}</span>
                            @if ($item->category)
                                <span class="text-stone-400">&middot;</span>
                                <span class="text-stone-500">{{ $item->category->name }}</span>
                            @endif
                        </div>
                        <span class="font-mono text-stone-400">{{ $item->created_at->format('d M Y') }}</span>
                    </div>

                    <h2 class="mt-2 text-sm font-semibold text-stone-950">{{ $item->judul_laporan }}</h2>
                    <p class="mt-1 text-xs text-stone-600 leading-relaxed line-clamp-2">{{ \Illuminate\Support\Str::limit($item->isi_laporan, 220) }}</p>

                    <div class="mt-3 flex items-center justify-between text-[11px] text-stone-500">
                        <span>{{ $item->public_reporter_name }}</span>
                        <span class="font-medium text-emerald-900">{{ $item->public_responses_count }} riwayat progres &rarr;</span>
                    </div>
                </a>
            @empty
                <div class="px-6 py-10 text-center text-sm text-stone-500">
                    Tidak ada laporan publik yang cocok dengan filter pencarian.
                </div>
            @endforelse
        </div>

        <div class="mt-5">
            {{ $reports->links() }}
        </div>
    </div>
@endsection
