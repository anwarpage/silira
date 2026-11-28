@extends('layouts.admin')

@section('title', 'Dashboard & Analitik — SILIRA')

@section('content')
    @php
        $stats = $summary['stats'];
        $total = max(1, $stats['total']);
        $exportParams = array_filter([
            'period' => $filters['period'] !== 'all' ? $filters['period'] : null,
            'date_from' => $filters['date_from'],
            'date_to' => $filters['date_to'],
            'category_id' => $filters['category_id'],
            'jenis' => $filters['jenis'],
            'privacy' => $filters['privacy'],
            'status' => $filters['status'],
        ], fn ($v) => $v !== null && $v !== '');
    @endphp

    {{-- Header + Google Forms-Style Export & Download Bar --}}
    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-4 pb-6 border-b border-[#e5e7eb]">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <p class="s-kicker">Panel Pengurus DPM FDIK</p>
                <span class="text-[#cbd5e1]" aria-hidden="true">&middot;</span>
                <span class="inline-flex items-center gap-1.5 text-[12px] font-medium px-2.5 py-0.5 rounded-[6px] bg-[#eff6ff] text-[#1e3a8a]">
                    {{ $periodLabel }}
                </span>
            </div>
            <div class="flex flex-wrap items-baseline gap-3">
                <h1 class="text-[30px] font-semibold text-[#0f172a]">Dashboard &amp; Analitik</h1>
                <span class="font-mono text-[14px] font-medium text-[#475569]">{{ number_format($stats['total']) }} respons</span>
            </div>
            <p class="text-[15px] text-[#475569] mt-1">Ringkasan visual, rincian per pertanyaan formulir, inspeksi individual, dan ekspor laporan.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 no-print">
            <a href="{{ route('admin.reports.index', ['status' => 'menunggu']) }}" class="s-btn-secondary whitespace-nowrap">
                Antrean Verifikasi ({{ $stats['menunggu'] }})
            </a>

            {{-- Export & Download Dropdown (CSV, Excel, PDF, Print) --}}
            <div class="relative">
                <button
                    type="button"
                    id="exportMenuTrigger"
                    aria-haspopup="true"
                    aria-expanded="false"
                    class="s-btn-primary whitespace-nowrap"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Ekspor &amp; Unduh</span>
                    <svg class="w-4 h-4 shrink-0 opacity-80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>

                <div
                    id="exportMenuPanel"
                    class="hidden absolute right-0 mt-2 w-72 rounded-[12px] bg-white border border-[#0f172a]/12 shadow-xl py-1.5 z-40"
                    role="menu"
                >
                    <div class="px-3.5 py-2 border-b border-[#e5e7eb]">
                        <p class="text-[11.5px] font-semibold uppercase tracking-[0.06em] text-[#64748b]">Unduh Sesuai Filter Aktif</p>
                    </div>

                    <a href="{{ route('admin.analytics.export.csv', $exportParams) }}" role="menuitem" class="flex items-start gap-3 px-3.5 py-2.5 hover:bg-[#f8fafc] transition">
                        <span class="mt-0.5 w-8 h-8 rounded-[8px] bg-[#eff6ff] text-[#1e3a8a] flex items-center justify-center font-mono text-[11px] font-semibold shrink-0">CSV</span>
                        <div>
                            <p class="text-[13.5px] font-semibold text-[#0f172a]">Unduh CSV (Spreadsheet)</p>
                            <p class="text-[12px] text-[#475569]">Siap impor ke Google Sheets &amp; Excel</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.analytics.export.excel', $exportParams) }}" role="menuitem" class="flex items-start gap-3 px-3.5 py-2.5 hover:bg-[#f8fafc] transition">
                        <span class="mt-0.5 w-8 h-8 rounded-[8px] bg-[#dcfce7] text-[#166534] flex items-center justify-center font-mono text-[11px] font-semibold shrink-0">XLS</span>
                        <div>
                            <p class="text-[13.5px] font-semibold text-[#0f172a]">Unduh Excel (.xls)</p>
                            <p class="text-[12px] text-[#475569]">2 sheet: Ringkasan Analitik &amp; Respons</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.analytics.export.pdf', $exportParams) }}" role="menuitem" class="flex items-start gap-3 px-3.5 py-2.5 hover:bg-[#f8fafc] transition">
                        <span class="mt-0.5 w-8 h-8 rounded-[8px] bg-[#fee2e2] text-[#991b1b] flex items-center justify-center font-mono text-[11px] font-semibold shrink-0">PDF</span>
                        <div>
                            <p class="text-[13.5px] font-semibold text-[#0f172a]">Unduh Laporan PDF (A4)</p>
                            <p class="text-[12px] text-[#475569]">Dokumen ringkasan eksekutif siap rapat</p>
                        </div>
                    </a>

                    <div class="my-1 border-t border-[#e5e7eb]"></div>

                    <button type="button" onclick="window.print()" role="menuitem" class="w-full text-left flex items-start gap-3 px-3.5 py-2.5 hover:bg-[#f8fafc] transition cursor-pointer">
                        <span class="mt-0.5 w-8 h-8 rounded-[8px] bg-[#f1f5f9] text-[#334155] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z" />
                            </svg>
                        </span>
                        <div>
                            <p class="text-[13.5px] font-semibold text-[#0f172a]">Cetak Halaman Analitik</p>
                            <p class="text-[12px] text-[#475569]">Cetak langsung grafik &amp; ringkasan</p>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Analytics Filter Bar --}}
    <form method="GET" action="{{ route('admin.dashboard') }}" class="mt-5 s-card p-4 no-print">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
            <div>
                <label for="filterPeriod" class="block text-[12px] font-medium text-[#475569] mb-1">Periode Waktu</label>
                <select id="filterPeriod" name="period" class="s-input s-input-sm">
                    @foreach ($periodOptions as $key => $label)
                        <option value="{{ $key }}" @selected($filters['period'] === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filterJenis" class="block text-[12px] font-medium text-[#475569] mb-1">Jenis Laporan</label>
                <select id="filterJenis" name="jenis" class="s-input s-input-sm">
                    <option value="">Semua Jenis</option>
                    @foreach (\App\Models\Report::JENIS_OPTIONS as $jenis)
                        <option value="{{ $jenis }}" @selected($filters['jenis'] === $jenis)>{{ $jenis }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filterCategory" class="block text-[12px] font-medium text-[#475569] mb-1">Kategori Bidang</label>
                <select id="filterCategory" name="category_id" class="s-input s-input-sm">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected($filters['category_id'] === $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filterPrivacy" class="block text-[12px] font-medium text-[#475569] mb-1">Privasi</label>
                <select id="filterPrivacy" name="privacy" class="s-input s-input-sm">
                    <option value="">Semua Privasi</option>
                    @foreach (\App\Models\Report::PRIVACY_OPTIONS as $key => $label)
                        <option value="{{ $key }}" @selected($filters['privacy'] === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filterStatus" class="block text-[12px] font-medium text-[#475569] mb-1">Status</label>
                <select id="filterStatus" name="status" class="s-input s-input-sm">
                    <option value="">Semua Status</option>
                    @foreach (\App\Models\Report::STATUSES as $key => $label)
                        <option value="{{ $key }}" @selected($filters['status'] === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="s-btn-primary h-[40px] px-4 text-[13.5px] flex-1">
                    Terapkan
                </button>
                @if ($hasActiveFilters)
                    <a href="{{ route('admin.dashboard') }}" class="s-btn-secondary h-[40px] px-3 text-[13.5px]" title="Reset semua filter">
                        Reset
                    </a>
                @endif
            </div>
        </div>

        {{-- Custom Date Range Row --}}
        <div id="customDateFields" class="{{ $filters['period'] === 'custom' ? 'grid' : 'hidden' }} grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 mt-3 pt-3 border-t border-[#e5e7eb]">
            <div class="lg:col-span-2">
                <label for="filterDateFrom" class="block text-[12px] font-medium text-[#475569] mb-1">Dari Tanggal</label>
                <input id="filterDateFrom" type="date" name="date_from" value="{{ $filters['date_from'] }}" class="s-input s-input-sm">
            </div>
            <div class="lg:col-span-2">
                <label for="filterDateTo" class="block text-[12px] font-medium text-[#475569] mb-1">Sampai Tanggal</label>
                <input id="filterDateTo" type="date" name="date_to" value="{{ $filters['date_to'] }}" class="s-input s-input-sm">
            </div>
        </div>
    </form>

    {{-- Google Forms 3-Tab Switcher: Ringkasan | Per Pertanyaan | Individual --}}
    <div class="mt-6 border-b border-[#e5e7eb] no-print" role="tablist" aria-label="Mode tampilan analitik">
        <div class="flex items-center gap-6 overflow-x-auto">
            <button
                type="button"
                role="tab"
                data-analytics-tab="summary"
                aria-selected="{{ $activeTab === 'summary' ? 'true' : 'false' }}"
                class="pb-3.5 text-[14.5px] border-b-2 transition whitespace-nowrap cursor-pointer {{ $activeTab === 'summary' ? 'text-[#1e3a8a] border-[#1e3a8a] font-semibold' : 'text-[#475569] border-transparent font-medium hover:text-[#0f172a]' }}"
            >
                Ringkasan
            </button>
            <button
                type="button"
                role="tab"
                data-analytics-tab="question"
                aria-selected="{{ $activeTab === 'question' ? 'true' : 'false' }}"
                class="pb-3.5 text-[14.5px] border-b-2 transition whitespace-nowrap cursor-pointer {{ $activeTab === 'question' ? 'text-[#1e3a8a] border-[#1e3a8a] font-semibold' : 'text-[#475569] border-transparent font-medium hover:text-[#0f172a]' }}"
            >
                Per Pertanyaan
            </button>
            <button
                type="button"
                role="tab"
                data-analytics-tab="individual"
                aria-selected="{{ $activeTab === 'individual' ? 'true' : 'false' }}"
                class="pb-3.5 text-[14.5px] border-b-2 transition whitespace-nowrap cursor-pointer {{ $activeTab === 'individual' ? 'text-[#1e3a8a] border-[#1e3a8a] font-semibold' : 'text-[#475569] border-transparent font-medium hover:text-[#0f172a]' }}"
            >
                Individual
                <span class="ml-1.5 font-mono text-[12px] px-1.5 py-0.5 rounded bg-[#f1f5f9] text-[#475569]">{{ $stats['total'] }}</span>
            </button>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- TAB 1: RINGKASAN (SUMMARY)                                       --}}
    {{-- ================================================================ --}}
    <div data-analytics-panel="summary" class="{{ $activeTab === 'summary' ? '' : 'hidden' }} mt-6 space-y-6">
        {{-- Unified Metric Strip --}}
        <div class="s-card-accent overflow-hidden">
            <div class="grid grid-cols-2 lg:grid-cols-4 divide-y lg:divide-y-0 divide-x divide-[#e5e7eb]">
                <div class="p-6">
                    <p class="text-[13px] font-medium text-[#475569]">Total Respons Masuk</p>
                    <p class="mt-2 text-[32px] font-medium font-mono text-[#0f172a] tabular-nums">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="p-6">
                    <p class="text-[13px] font-medium text-[#475569]">Menunggu Verifikasi</p>
                    <p class="mt-2 text-[32px] font-medium font-mono text-[#92400e] tabular-nums">{{ number_format($stats['menunggu']) }}</p>
                </div>
                <div class="p-6">
                    <p class="text-[13px] font-medium text-[#475569]">Diverifikasi &amp; Diproses</p>
                    <p class="mt-2 text-[32px] font-medium font-mono text-[#1e3a8a] tabular-nums">{{ number_format($stats['active']) }}</p>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-[13px] font-medium text-[#475569]">Tuntas</p>
                        <span class="font-mono text-[12.5px] font-medium text-[#14532d] bg-[#dcfce7] px-2 py-0.5 rounded-[6px]">{{ $stats['completion_rate'] }}%</span>
                    </div>
                    <p class="mt-2 text-[32px] font-medium font-mono text-[#14532d] tabular-nums">{{ number_format($stats['selesai']) }}</p>
                </div>
            </div>

            {{-- Secondary SLA & Engagement Strip --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 px-6 py-3.5 bg-[#f8fafc] border-t border-[#e5e7eb] text-[13px]">
                <div class="flex items-center justify-between lg:justify-start lg:gap-2">
                    <span class="text-[#475569]">Rata-rata verifikasi:</span>
                    <strong class="font-mono text-[#0f172a]">{{ $avgVerifyLabel }}</strong>
                </div>
                <div class="flex items-center justify-between lg:justify-start lg:gap-2">
                    <span class="text-[#475569]">Rata-rata tuntas:</span>
                    <strong class="font-mono text-[#0f172a]">{{ $avgResolveLabel }}</strong>
                </div>
                <div class="flex items-center justify-between lg:justify-start lg:gap-2">
                    <span class="text-[#475569]">Dengan lampiran:</span>
                    <strong class="font-mono text-[#0f172a]">{{ $stats['with_attachment'] }} ({{ $stats['with_attachment_rate'] }}%)</strong>
                </div>
                <div class="flex items-center justify-between lg:justify-start lg:gap-2">
                    <span class="text-[#475569]">Total tanggapan:</span>
                    <strong class="font-mono text-[#0f172a]">{{ number_format($stats['total_responses']) }}</strong>
                </div>
            </div>
        </div>

        {{-- Row 1: Timeline Trend (8 cols) + Weekday Pattern (4 cols) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-8 s-card p-6">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="font-sans text-[16px] font-semibold text-[#0f172a]">Tren laporan masuk</h2>
                        <p class="text-[13px] text-[#475569] mt-0.5">Skala {{ strtolower($summary['timeline']['granularity']) }} &middot; {{ $periodLabel }}</p>
                    </div>
                    <div class="flex items-center gap-1.5 no-print">
                        <button type="button" data-chart-copy="timeline" title="Salin grafik ke clipboard" class="h-8 px-2.5 rounded-[7px] border border-[#0f172a]/12 text-[12px] font-medium text-[#475569] hover:text-[#0f172a] hover:bg-[#f8fafc] transition cursor-pointer">
                            Salin
                        </button>
                        <button type="button" data-chart-download="timeline" title="Unduh grafik PNG" class="h-8 px-2.5 rounded-[7px] border border-[#0f172a]/12 text-[12px] font-medium text-[#475569] hover:text-[#0f172a] hover:bg-[#f8fafc] transition cursor-pointer">
                            PNG
                        </button>
                    </div>
                </div>
                <div class="mt-4 h-[240px]">
                    <canvas id="chartTimeline"></canvas>
                </div>
            </div>

            <div class="lg:col-span-4 s-card p-6">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="font-sans text-[16px] font-semibold text-[#0f172a]">Pola hari masuk</h2>
                        <p class="text-[13px] text-[#475569] mt-0.5">Distribusi Senin–Minggu</p>
                    </div>
                    <div class="flex items-center gap-1.5 no-print">
                        <button type="button" data-chart-copy="weekday" title="Salin grafik" class="h-8 px-2.5 rounded-[7px] border border-[#0f172a]/12 text-[12px] font-medium text-[#475569] hover:text-[#0f172a] hover:bg-[#f8fafc] transition cursor-pointer">
                            Salin
                        </button>
                        <button type="button" data-chart-download="weekday" title="Unduh PNG" class="h-8 px-2.5 rounded-[7px] border border-[#0f172a]/12 text-[12px] font-medium text-[#475569] hover:text-[#0f172a] hover:bg-[#f8fafc] transition cursor-pointer">
                            PNG
                        </button>
                    </div>
                </div>
                <div class="mt-4 h-[240px]">
                    <canvas id="chartWeekday"></canvas>
                </div>
            </div>
        </div>

        {{-- Row 2: Jenis Laporan (Pie/Donut) + Tingkat Privasi (Pie/Donut) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-6 s-card p-6">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="font-sans text-[16px] font-semibold text-[#0f172a]">Jenis laporan</h2>
                        <p class="text-[13px] text-[#475569] mt-0.5">{{ $stats['total'] }} respons</p>
                    </div>
                    <div class="flex items-center gap-1.5 no-print">
                        <button type="button" data-chart-copy="jenis" class="h-8 px-2.5 rounded-[7px] border border-[#0f172a]/12 text-[12px] font-medium text-[#475569] hover:text-[#0f172a] hover:bg-[#f8fafc] transition cursor-pointer">Salin</button>
                        <button type="button" data-chart-download="jenis" class="h-8 px-2.5 rounded-[7px] border border-[#0f172a]/12 text-[12px] font-medium text-[#475569] hover:text-[#0f172a] hover:bg-[#f8fafc] transition cursor-pointer">PNG</button>
                    </div>
                </div>
                <div class="mt-5 grid grid-cols-1 sm:grid-cols-12 gap-5 items-center">
                    <div class="sm:col-span-6 h-[190px]">
                        <canvas id="chartJenis"></canvas>
                    </div>
                    <div class="sm:col-span-6 space-y-3">
                        @foreach ($summary['jenis'] as $item)
                            <div class="flex items-center justify-between gap-3 text-[13.5px]">
                                <span class="inline-flex items-center gap-2 text-[#0f172a] min-w-0">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $item['color'] }}"></span>
                                    <span class="truncate">{{ $item['label'] }}</span>
                                </span>
                                <span class="font-mono text-[13px] text-[#475569] shrink-0">
                                    <strong class="text-[#0f172a]">{{ $item['count'] }}</strong> ({{ $item['percent'] }}%)
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="lg:col-span-6 s-card p-6">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="font-sans text-[16px] font-semibold text-[#0f172a]">Tingkat privasi pelapor</h2>
                        <p class="text-[13px] text-[#475569] mt-0.5">{{ $stats['total'] }} respons</p>
                    </div>
                    <div class="flex items-center gap-1.5 no-print">
                        <button type="button" data-chart-copy="privacy" class="h-8 px-2.5 rounded-[7px] border border-[#0f172a]/12 text-[12px] font-medium text-[#475569] hover:text-[#0f172a] hover:bg-[#f8fafc] transition cursor-pointer">Salin</button>
                        <button type="button" data-chart-download="privacy" class="h-8 px-2.5 rounded-[7px] border border-[#0f172a]/12 text-[12px] font-medium text-[#475569] hover:text-[#0f172a] hover:bg-[#f8fafc] transition cursor-pointer">PNG</button>
                    </div>
                </div>
                <div class="mt-5 grid grid-cols-1 sm:grid-cols-12 gap-5 items-center">
                    <div class="sm:col-span-6 h-[190px]">
                        <canvas id="chartPrivacy"></canvas>
                    </div>
                    <div class="sm:col-span-6 space-y-3">
                        @foreach ($summary['privacy'] as $item)
                            <div class="flex items-center justify-between gap-3 text-[13.5px]">
                                <span class="inline-flex items-center gap-2 text-[#0f172a] min-w-0">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $item['color'] }}"></span>
                                    <span class="truncate">{{ $item['label'] }}</span>
                                </span>
                                <span class="font-mono text-[13px] text-[#475569] shrink-0">
                                    <strong class="text-[#0f172a]">{{ $item['count'] }}</strong> ({{ $item['percent'] }}%)
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 3: Kategori Bidang (Horizontal Bar) + Status Penanganan (Bar) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-7 s-card p-6">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="font-sans text-[16px] font-semibold text-[#0f172a]">Distribusi kategori bidang</h2>
                        <p class="text-[13px] text-[#475569] mt-0.5">Diurutkan dari kategori terbanyak</p>
                    </div>
                    <div class="flex items-center gap-1.5 no-print">
                        <button type="button" data-chart-copy="category" class="h-8 px-2.5 rounded-[7px] border border-[#0f172a]/12 text-[12px] font-medium text-[#475569] hover:text-[#0f172a] hover:bg-[#f8fafc] transition cursor-pointer">Salin</button>
                        <button type="button" data-chart-download="category" class="h-8 px-2.5 rounded-[7px] border border-[#0f172a]/12 text-[12px] font-medium text-[#475569] hover:text-[#0f172a] hover:bg-[#f8fafc] transition cursor-pointer">PNG</button>
                    </div>
                </div>
                <div class="mt-4 h-[260px]">
                    <canvas id="chartCategory"></canvas>
                </div>
            </div>

            <div class="lg:col-span-5 s-card p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="font-sans text-[16px] font-semibold text-[#0f172a]">Status penanganan</h2>
                            <p class="text-[13px] text-[#475569] mt-0.5">Sebaran tahap tindak lanjut</p>
                        </div>
                        <div class="flex items-center gap-1.5 no-print">
                            <button type="button" data-chart-copy="status" class="h-8 px-2.5 rounded-[7px] border border-[#0f172a]/12 text-[12px] font-medium text-[#475569] hover:text-[#0f172a] hover:bg-[#f8fafc] transition cursor-pointer">Salin</button>
                            <button type="button" data-chart-download="status" class="h-8 px-2.5 rounded-[7px] border border-[#0f172a]/12 text-[12px] font-medium text-[#475569] hover:text-[#0f172a] hover:bg-[#f8fafc] transition cursor-pointer">PNG</button>
                        </div>
                    </div>
                    <div class="mt-4 h-[210px]">
                        <canvas id="chartStatus"></canvas>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-[#e5e7eb]">
                    <div class="w-full h-2.5 rounded-[6px] bg-[#f8fafc] overflow-hidden flex">
                        @foreach ($summary['status'] as $item)
                            <div class="h-full" style="width: {{ $item['percent'] }}%; background-color: {{ $item['color'] }}" title="{{ $item['label'] }}: {{ $item['count'] }}"></div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Row 4: Jurusan/Instansi (Horizontal Bar) + Scrollable Short-Answer Highlights --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-6 s-card p-6">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="font-sans text-[16px] font-semibold text-[#0f172a]">Sebaran jurusan / instansi</h2>
                        <p class="text-[13px] text-[#475569] mt-0.5">8 asal pelapor teratas</p>
                    </div>
                    <div class="flex items-center gap-1.5 no-print">
                        <button type="button" data-chart-copy="jurusan" class="h-8 px-2.5 rounded-[7px] border border-[#0f172a]/12 text-[12px] font-medium text-[#475569] hover:text-[#0f172a] hover:bg-[#f8fafc] transition cursor-pointer">Salin</button>
                        <button type="button" data-chart-download="jurusan" class="h-8 px-2.5 rounded-[7px] border border-[#0f172a]/12 text-[12px] font-medium text-[#475569] hover:text-[#0f172a] hover:bg-[#f8fafc] transition cursor-pointer">PNG</button>
                    </div>
                </div>
                @if (count($summary['jurusan']) > 0)
                    <div class="mt-4 h-[250px]">
                        <canvas id="chartJurusan"></canvas>
                    </div>
                @else
                    <p class="mt-8 text-[14px] text-[#475569] text-center">Belum ada data jurusan atau instansi.</p>
                @endif
            </div>

            <div class="lg:col-span-6 s-card p-6 flex flex-col">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="font-sans text-[16px] font-semibold text-[#0f172a]">Ringkasan jawaban teks terbaru</h2>
                        <p class="text-[13px] text-[#475569] mt-0.5">Judul laporan &amp; lokasi kejadian</p>
                    </div>
                    <span class="font-mono text-[12px] text-[#475569]">{{ count($summary['textHighlights']) }} ditampilkan</span>
                </div>

                <div class="mt-4 space-y-2 max-h-[250px] overflow-y-auto pr-1">
                    @forelse ($summary['textHighlights'] as $hl)
                        <a href="{{ $hl['url'] }}" class="block px-3.5 py-2.5 rounded-[9px] bg-[#f8fafc] hover:bg-[#eff6ff] border border-[#0f172a]/7 transition">
                            <p class="text-[13.5px] font-medium text-[#0f172a] line-clamp-1">{{ $hl['judul_laporan'] }}</p>
                            <div class="mt-1 flex flex-wrap items-center justify-between gap-2 text-[12px] text-[#475569]">
                                <span class="truncate">{{ $hl['lokasi_kejadian'] ?: $hl['category'] }}</span>
                                <span class="font-mono text-[#64748b] shrink-0">{{ $hl['ticket_code'] }}</span>
                            </div>
                        </a>
                    @empty
                        <p class="py-10 text-center text-[14px] text-[#475569]">Belum ada laporan pada filter ini.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Operational Recent Reports Table --}}
        <div class="s-card overflow-hidden">
            <div class="px-6 py-4 border-b border-[#e5e7eb] flex items-center justify-between">
                <h2 class="font-sans text-[16px] font-semibold text-[#0f172a]">Laporan terbaru</h2>
                <a href="{{ route('admin.reports.index') }}" class="text-[14px] s-link no-print">Kelola semua &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-[14px]">
                    <thead class="bg-[#f8fafc] text-[12.5px] font-medium text-[#475569] border-b border-[#e5e7eb]">
                        <tr>
                            <th class="px-6 py-3">Tiket</th>
                            <th class="px-6 py-3">Laporan</th>
                            <th class="px-6 py-3">Pelapor</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right no-print">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#e5e7eb]">
                        @forelse ($recentReports as $item)
                            <tr class="hover:bg-[#f8fafc] transition">
                                <td class="px-6 py-4 font-mono text-[13px] text-[#475569] whitespace-nowrap">{{ $item->ticket_code }}</td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-[#0f172a] line-clamp-1">{{ $item->judul_laporan }}</p>
                                    <p class="text-[13px] text-[#475569]">{{ $item->jenis_laporan }} &middot; {{ $item->category?->name ?? '-' }} &middot; {{ $item->privacy_label }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-[#0f172a]">{{ $item->nama_pelapor }}</p>
                                    <p class="font-mono text-[12.5px] text-[#475569]">{{ $item->nim_nip }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-[6px] text-[12.5px] font-medium {{ $item->status_badge_class }}">{{ $item->status_label }}</span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap no-print">
                                    <a href="{{ route('admin.reports.show', $item) }}" class="s-btn-secondary h-[34px] px-3.5 text-[13px]">
                                        Buka
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-[#475569]">Belum ada laporan masuk pada filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- TAB 2: PER PERTANYAAN (LAZY-LOADED QUESTION EXPLORER)            --}}
    {{-- ================================================================ --}}
    <div data-analytics-panel="question" class="{{ $activeTab === 'question' ? '' : 'hidden' }} mt-6 space-y-5">
        <div class="s-card p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex-1 max-w-md">
                <label for="questionSelect" class="sr-only">Pilih pertanyaan formulir</label>
                <select id="questionSelect" class="s-input s-input-sm" disabled>
                    <option value="0">Memuat daftar pertanyaan...</option>
                </select>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" id="questionPrev" disabled aria-label="Pertanyaan sebelumnya" class="s-btn-secondary h-[38px] px-3 text-[13.5px]">
                    &larr; Sebelumnya
                </button>
                <span id="questionCounter" class="font-mono text-[13px] text-[#475569] px-2">—</span>
                <button type="button" id="questionNext" disabled aria-label="Pertanyaan berikutnya" class="s-btn-secondary h-[38px] px-3 text-[13.5px]">
                    Berikutnya &rarr;
                </button>
            </div>
        </div>

        <div id="questionContent"></div>
    </div>

    {{-- ================================================================ --}}
    {{-- TAB 3: INDIVIDUAL (LAZY-LOADED SERVER-PAGINATED VIEWER)          --}}
    {{-- ================================================================ --}}
    <div data-analytics-panel="individual" class="{{ $activeTab === 'individual' ? '' : 'hidden' }} mt-6 space-y-5">
        <div class="s-card p-4 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" id="individualPrev" disabled aria-label="Respons sebelumnya" class="s-btn-secondary h-[38px] px-3 text-[13.5px]">
                    &larr;
                </button>
                <div class="inline-flex items-center gap-2 text-[13.5px] text-[#475569]">
                    <span>Respons</span>
                    <input
                        type="number"
                        id="individualJump"
                        min="1"
                        max="{{ max(1, $stats['total']) }}"
                        value="1"
                        aria-label="Nomor respons"
                        class="w-16 h-[38px] px-2 text-center font-mono text-[13.5px] rounded-[8px] border border-[#0f172a]/16 text-[#0f172a]"
                    >
                    <span>dari <strong id="individualTotalLabel" class="font-mono text-[#0f172a]">{{ $stats['total'] }}</strong></span>
                </div>
                <button type="button" id="individualNext" disabled aria-label="Respons berikutnya" class="s-btn-secondary h-[38px] px-3 text-[13.5px]">
                    &rarr;
                </button>

                <select id="individualSelect" aria-label="Lompat ke tiket" class="hidden s-input s-input-sm sm:w-64 ml-0 sm:ml-2"></select>
            </div>

            <div id="individualActions" class="hidden flex-wrap items-center gap-2 shrink-0">
                <a id="individualPdfLink" href="#" class="s-btn-secondary h-[38px] px-3.5 text-[13.5px]">
                    Unduh PDF Respons
                </a>
                <a id="individualOpenLink" href="#" class="s-btn-primary h-[38px] px-3.5 text-[13.5px]">
                    Buka Tindak Lanjut &rarr;
                </a>
            </div>
        </div>

        <div id="individualContent"></div>
    </div>

    {{-- Subtle Toast for Copy/Download Chart --}}
    <div id="analyticsToast" role="status" aria-live="polite" class="hidden fixed bottom-6 right-6 z-50 px-4 py-2.5 rounded-[10px] bg-[#0f172a] text-white text-[13.5px] font-medium shadow-lg transition duration-150 opacity-0 translate-y-2 no-print"></div>

    {{-- Lean Analytics JSON Payload (Summary + Lazy-Load Endpoints) --}}
    <script id="siliraAnalyticsData" type="application/json">{!! json_encode([
        'summary' => $summary,
        'activeTab' => $activeTab,
        'questionsUrl' => route('admin.analytics.questions', $exportParams),
        'individualUrl' => route('admin.analytics.individual', $exportParams),
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>

    @vite(['resources/js/analytics.js'])
@endsection
