@extends('layouts.public')

@section('title', 'Direktori Aspirasi Publik — SILIRA')

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
    @endphp

    {{-- Institutional Page Header Band --}}
    <div class="bg-white border-b border-[#e5e7eb]">
        <div class="max-w-[1180px] mx-auto px-5 sm:px-8 py-9">
            <nav aria-label="Breadcrumb" class="text-[13px] text-[#475569] mb-3">
                <a href="{{ route('home') }}" class="hover:text-[#1e3a8a] transition">Beranda</a>
                <span class="mx-2 text-[#d0d7de]" aria-hidden="true">/</span>
                <span class="text-[#0f172a] font-medium">Direktori Aspirasi</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <div>
                    <h1 class="text-[30px] sm:text-[36px] font-semibold leading-tight text-[#0f172a]">
                        Direktori Aspirasi Publik
                    </h1>
                    <p class="mt-2 text-[15.5px] text-[#475569]">
                        Register terbuka laporan berstatus Publik dan Anonim. Laporan berstatus Rahasia tidak dipublikasikan.
                    </p>
                </div>
                <a href="{{ route('tracking.index') }}" class="s-btn-secondary h-[42px] px-4 text-[14px] shrink-0">
                    Lacak Tiket Saya &rarr;
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-[1180px] mx-auto px-5 sm:px-8 py-9">
        <form method="GET" action="{{ route('public.feed') }}" class="s-card p-5 space-y-3.5">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 min-w-0">
                    <label for="feed_q" class="sr-only">Kata kunci pencarian</label>
                    <input type="text" id="feed_q" name="q" value="{{ request('q') }}" placeholder="Cari judul, uraian, atau nomor tiket..." class="s-input s-input-sm">
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button type="submit" class="s-btn-primary h-[40px] px-5 text-[14px]">Cari Berkas</button>
                    @if (request()->hasAny(['q', 'category_id', 'jenis', 'status']))
                        <a href="{{ route('public.feed') }}" class="s-btn-secondary h-[40px] px-3.5 text-[14px]">Reset</a>
                    @endif
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <x-select-dropdown name="category_id" :options="$categoryOptions" :selected="request('category_id')" placeholder="Semua kategori" size="sm" :searchable="true" />
                </div>
                <div>
                    <x-select-dropdown name="jenis" :options="$jenisOptions" :selected="request('jenis')" placeholder="Semua klasifikasi" size="sm" />
                </div>
                <div>
                    <x-select-dropdown name="status" :options="$statusOptions" :selected="request('status')" placeholder="Semua status" size="sm" />
                </div>
            </div>
        </form>

        <div class="mt-6 s-card overflow-hidden">
            <div class="divide-y divide-[#e5e7eb]">
                @forelse ($reports as $item)
                    <a href="{{ route('public.aspirations.show', $item->ticket_code) }}" class="group block px-6 py-5 hover:bg-[#f8fafc] transition border-l-2 border-l-transparent hover:border-l-[#1e3a8a]">
                        <div class="flex flex-wrap items-center justify-between gap-2 text-[13px]">
                            <div class="flex flex-wrap items-center gap-2.5">
                                <span class="font-mono font-medium text-[#0f172a]">{{ $item->ticket_code }}</span>
                                <span class="px-2.5 py-0.5 rounded-[6px] font-medium {{ $item->status_badge_class }}">{{ $item->status_label }}</span>
                                <span class="text-[#475569]">{{ $item->jenis_laporan }}</span>
                                @if ($item->category)
                                    <span class="text-[#475569]">&middot; {{ $item->category->name }}</span>
                                @endif
                            </div>
                            <time class="font-mono text-[#475569]">{{ $item->created_at->format('d M Y') }}</time>
                        </div>

                        <h2 class="mt-2 font-sans text-[18px] font-semibold text-[#0f172a] group-hover:text-[#1e3a8a] transition break-words [overflow-wrap:anywhere]">
                            {{ $item->judul_laporan }}
                        </h2>
                        <p class="mt-1.5 text-[15px] text-[#475569] leading-relaxed break-words [overflow-wrap:anywhere]">
                            {{ \Illuminate\Support\Str::limit($item->isi_laporan, 220) }}
                        </p>

                        <div class="mt-3 flex items-center justify-between text-[13px] text-[#475569]">
                            <span>Pelapor: <strong class="font-medium text-[#0f172a]">{{ $item->public_reporter_name }}</strong></span>
                            <span class="font-medium text-[#1e3a8a]">{{ $item->public_responses_count }} riwayat tindak lanjut &rarr;</span>
                        </div>
                    </a>
                @empty
                    <div class="px-6 py-14 text-center text-[15px] text-[#475569]">
                        Tidak ada laporan publik yang cocok dengan filter pencarian.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="mt-6">
            {{ $reports->links() }}
        </div>
    </div>
@endsection
