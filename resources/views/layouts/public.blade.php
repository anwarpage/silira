<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'Silira — Portal aspirasi DPM Fakultas Dakwah dan Ilmu Komunikasi')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Onest:wght@400..600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">
    <header class="no-print sticky top-0 z-40 h-[72px] bg-[#f5f7fa] border-b border-[#0f172a]/10">
        <div class="max-w-[1200px] mx-auto px-6 sm:px-10 h-full flex items-center justify-between gap-6">
            <a href="{{ route('home') }}" class="hover:opacity-80 transition">
                <x-brand-logo />
            </a>

            <nav class="hidden md:flex items-center gap-7 text-[15px] font-medium">
                <a href="{{ route('home') }}" class="py-1 transition {{ request()->routeIs('home') ? 'text-[#0f172a] border-b-2 border-[#0f172a]' : 'text-[#475569] hover:text-[#0f172a]' }}">Beranda</a>
                <a href="{{ route('public.feed') }}" class="py-1 transition {{ request()->routeIs('public.feed') ? 'text-[#0f172a] border-b-2 border-[#0f172a]' : 'text-[#475569] hover:text-[#0f172a]' }}">Aspirasi</a>
                <a href="{{ route('tracking.index') }}" class="py-1 transition {{ request()->routeIs('tracking.*') ? 'text-[#0f172a] border-b-2 border-[#0f172a]' : 'text-[#475569] hover:text-[#0f172a]' }}">Lacak</a>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="py-1 text-[#475569] hover:text-[#0f172a] transition">Dashboard</a>
                @else
                    <a href="{{ route('admin.login') }}" class="py-1 text-[#475569] hover:text-[#0f172a] transition">Admin</a>
                @endauth
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ route('reports.create') }}" class="s-btn-primary">
                    Kirim aspirasi
                </a>
            </div>
        </div>
    </header>

    {{-- Mobile sub-bar --}}
    <div class="no-print md:hidden bg-[#f5f7fa] border-b border-[#0f172a]/10 px-6 py-2.5 flex items-center gap-5 overflow-x-auto text-[14px] font-medium">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-[#0f172a] underline underline-offset-4' : 'text-[#475569]' }}">Beranda</a>
        <a href="{{ route('public.feed') }}" class="{{ request()->routeIs('public.feed') ? 'text-[#0f172a] underline underline-offset-4' : 'text-[#475569]' }}">Aspirasi</a>
        <a href="{{ route('tracking.index') }}" class="{{ request()->routeIs('tracking.*') ? 'text-[#0f172a] underline underline-offset-4' : 'text-[#475569]' }}">Lacak</a>
        <a href="{{ auth()->check() ? route('admin.dashboard') : route('admin.login') }}" class="text-[#475569]">{{ auth()->check() ? 'Dashboard' : 'Admin' }}</a>
    </div>

    <main class="flex-1">
        @yield('content')
    </main>

    {{-- Closing CTA + Structured Institutional Directory Footer --}}
    <footer class="no-print bg-[#0f172a] text-[#f8fafc] mt-16">
        <div class="max-w-[1200px] mx-auto px-6 sm:px-10">
            {{-- Band 1: Closing Action --}}
            <div class="py-12 border-b border-white/10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="max-w-xl">
                    <h2 class="text-[24px] sm:text-[28px] font-medium leading-tight text-white">
                        Sampaikan aspirasi Anda untuk kemajuan fakultas.
                    </h2>
                    <p class="mt-2 text-[15px] text-[#94a3b8]">
                        Tanpa perlu login. Laporan diterima resmi oleh Komisi Aspirasi DPM FDIK dan dapat dipantau melalui kode tiket.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <a href="{{ route('reports.create') }}" class="inline-flex items-center justify-center gap-2 h-[44px] px-6 rounded-[8px] bg-white text-[#0f172a] text-[15px] font-medium hover:bg-[#f1f5f9] transition">
                        Kirim aspirasi &rarr;
                    </a>
                    <a href="{{ route('tracking.index') }}" class="inline-flex items-center justify-center h-[44px] px-5 rounded-[8px] border border-white/20 text-white text-[15px] font-medium hover:bg-white/10 transition">
                        Lacak tiket
                    </a>
                </div>
            </div>

            {{-- Band 2: Structured Directory --}}
            <div class="py-12 grid grid-cols-1 md:grid-cols-12 gap-10 border-b border-white/10">
                <div class="md:col-span-5">
                    <x-brand-logo :inverted="true" />
                    <p class="mt-4 text-[14px] text-[#94a3b8] leading-relaxed max-w-sm">
                        Sistem Integrasi Layanan Interaksi dan Respon Aspirasi — Dewan Perwakilan Mahasiswa Fakultas Dakwah dan Ilmu Komunikasi.
                    </p>
                </div>

                <div class="md:col-span-7 grid grid-cols-2 sm:grid-cols-3 gap-8 text-[14px]">
                    <div>
                        <p class="font-medium text-white mb-3">Layanan</p>
                        <ul class="space-y-2.5 text-[#94a3b8]">
                            <li><a href="{{ route('reports.create') }}" class="hover:text-white transition">Kirim aspirasi</a></li>
                            <li><a href="{{ route('tracking.index') }}" class="hover:text-white transition">Lacak kode tiket</a></li>
                            <li><a href="{{ route('public.feed') }}" class="hover:text-white transition">Aspirasi publik</a></li>
                        </ul>
                    </div>

                    <div>
                        <p class="font-medium text-white mb-3">Privasi</p>
                        <ul class="space-y-2.5 text-[#94a3b8]">
                            <li><a href="{{ route('public.feed') }}" class="hover:text-white transition">Laporan publik</a></li>
                            <li><a href="{{ route('reports.create') }}" class="hover:text-white transition">Mode anonim</a></li>
                            <li><a href="{{ route('reports.create') }}" class="hover:text-white transition">Mode rahasia</a></li>
                        </ul>
                    </div>

                    <div>
                        <p class="font-medium text-white mb-3">Pengurus DPM</p>
                        <ul class="space-y-2.5 text-[#94a3b8]">
                            @auth
                                <li><a href="{{ route('admin.dashboard') }}" class="hover:text-white transition">Dashboard admin</a></li>
                                <li><a href="{{ route('admin.reports.index') }}" class="hover:text-white transition">Kelola laporan</a></li>
                            @else
                                <li><a href="{{ route('admin.login') }}" class="hover:text-white transition">Masuk portal admin</a></li>
                            @endauth
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Legal Row --}}
            <div class="py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[13px] text-[#94a3b8]">
                <span>&copy; {{ date('Y') }} Silira &middot; DPM Fakultas Dakwah dan Ilmu Komunikasi</span>
                <span>Layanan Aspirasi &amp; Pengaduan Terpadu</span>
            </div>
        </div>
    </footer>
</body>
</html>
