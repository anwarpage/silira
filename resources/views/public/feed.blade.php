@extends('layouts.public')

@section('title', 'Aspirasi publik — Silira')

@section('content')
    @php
        $categoryOptions = collect([['value' => '', 'label' => 'Semua kategori']])
            ->merge($categories->map(fn ($c) => ['value' => $c->id, 'label' => $c->name]))
            ->all();

        $jenisOptions = collect([['value' => '', 'label' => 'Semua klasifikasi']])
            ->merge(collect(\App\Models\Report::JENIS_OPTIONS)->map(fn ($j) => ['value' => $j, 'label' => $j]))
            ->all();

        $statusDots = [
            'menunggu' => 'bg-[#d97706]',
            'diverifikasi' => 'bg-[#0284c7]',
            'diproses' => 'bg-[#1e3a8a]',
            'selesai' => 'bg-[#1e3a8a]',
            'ditolak' => 'bg-[#dc2626]',
        ];
        $statusOptions = collect([['value' => '', 'label' => 'Semua status']])
            ->merge(collect(\App\Models\Report::STATUSES)->map(fn ($label, $key) => [
                'value' => $key,
                'label' => $label,
                'dot' => $statusDots[$key] ?? null,
            ]))
            ->all();
    @endphp

    <div class="max-w-[1200px] mx-auto px-6 sm:px-10 py-12">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <h1 class="text-[32px] font-medium leading-tight text-[#0f172a]">Aspirasi publik</h1>
                <p class="mt-1.5 text-[15px] text-[#475569]">Menampilkan laporan Publik dan Anonim. Laporan Rahasia tidak dipublikasikan.</p>
            </div>
            <a href="{{ route('reports.create') }}" class="s-btn-primary">
                Kirim aspirasi
            </a>
        </div>

        <form method="GET" action="{{ route('public.feed') }}" class="mt-7 s-card p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            <div class="lg:col-span-4">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul, isi, atau kode tiket..." class="s-input s-input-sm">
            </div>
            <div class="lg:col-span-3">
                <x-select-dropdown name="category_id" :options="$categoryOptions" :selected="request('category_id')" placeholder="Semua kategori" size="sm" :searchable="true" />
            </div>
            <div class="lg:col-span-2">
                <x-select-dropdown name="jenis" :options="$jenisOptions" :selected="request('jenis')" placeholder="Semua klasifikasi" size="sm" />
            </div>
            <div class="lg:col-span-2">
                <x-select-dropdown name="status" :options="$statusOptions" :selected="request('status')" placeholder="Semua status" size="sm" />
            </div>
            <div class="lg:col-span-1 flex gap-1.5">
                <button type="submit" class="s-btn-primary flex-1 h-[42px] px-3 text-[14px]">Cari</button>
                @if (request()->hasAny(['q', 'category_id', 'jenis', 'status']))
                    <a href="{{ route('public.feed') }}" title="Reset" class="s-btn-secondary h-[42px] px-3">&times;</a>
                @endif
            </div>
        </form>

        <div class="mt-6 divide-y divide-[#0f172a]/13 border-t border-b border-[#0f172a]/13">
            @forelse ($reports as $item)
                <a href="{{ route('tracking.show', $item->ticket_code) }}" class="block py-5 px-3 -mx-3 rounded-[8px] hover:bg-[#eceff4]/50 transition">
                    <div class="flex flex-wrap items-center justify-between gap-2 text-[13px]">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <span class="font-mono text-[#475569]">{{ $item->ticket_code }}</span>
                            <span class="px-2.5 py-0.5 rounded-[6px] font-medium {{ $item->status_badge_class }}">{{ $item->status_label }}</span>
                            <span class="text-[#475569]">{{ $item->jenis_laporan }}</span>
                            @if ($item->category)
                                <span class="text-[#475569]">&middot; {{ $item->category->name }}</span>
                            @endif
                        </div>
                        <span class="font-mono text-[#475569]">{{ $item->created_at->format('d M Y') }}</span>
                    </div>

                    <h2 class="mt-2 text-[18px] font-medium text-[#0f172a]">{{ $item->judul_laporan }}</h2>
                    <p class="mt-1.5 text-[15px] text-[#475569] leading-relaxed">{{ \Illuminate\Support\Str::limit($item->isi_laporan, 220) }}</p>

                    <div class="mt-3 flex items-center justify-between text-[13px] text-[#475569]">
                        <span>{{ $item->public_reporter_name }}</span>
                        <span class="font-medium text-[#1e3a8a]">{{ $item->public_responses_count }} riwayat progres &rarr;</span>
                    </div>
                </a>
            @empty
                <div class="py-12 text-center text-[15px] text-[#475569]">
                    Tidak ada laporan publik yang cocok dengan filter pencarian.
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $reports->links() }}
        </div>
    </div>
@endsection
