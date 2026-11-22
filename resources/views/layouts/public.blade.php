<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SILIRA — Layanan Aspirasi & Pengaduan DPM FDIK')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|jetbrains-mono:500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FAFAFA] text-slate-900 antialiased min-h-screen flex flex-col">
    <header class="no-print sticky top-0 z-40 bg-white/90 backdrop-blur-md border-b border-slate-200/80">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="hover:opacity-90 transition">
                <x-brand-logo subtitle="DPM Fakultas Dakwah & Ilmu Komunikasi" />
            </a>

            <nav class="hidden md:flex items-center gap-1 text-sm font-medium">
                <a href="{{ route('home') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('home') ? 'bg-slate-900 text-white font-semibold' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100' }}">Beranda</a>
                <a href="{{ route('reports.create') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('reports.*') ? 'bg-slate-900 text-white font-semibold' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100' }}">Buat Laporan</a>
                <a href="{{ route('tracking.index') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('tracking.*') ? 'bg-slate-900 text-white font-semibold' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100' }}">Lacak Tiket</a>
                <a href="{{ route('public.feed') }}" class="px-3.5 py-2 rounded-lg transition {{ request()->routeIs('public.feed') ? 'bg-slate-900 text-white font-semibold' : 'text-slate-600 hover:text-slate-950 hover:bg-slate-100' }}">Aspirasi Publik</a>
            </nav>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="btn-press inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold bg-slate-900 text-white hover:bg-slate-800">
                        Dashboard DPM
                    </a>
                @else
                    <a href="{{ route('admin.login') }}" class="btn-press inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold border border-slate-300 bg-white text-slate-700 hover:bg-slate-50 hover:border-slate-400">
                        Portal Admin
                    </a>
                @endauth
            </div>
        </div>

        {{-- Mobile quick nav --}}
        <div class="md:hidden border-t border-slate-100 px-4 py-2 flex items-center gap-1.5 overflow-x-auto text-xs font-medium bg-white">
            <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-lg whitespace-nowrap {{ request()->routeIs('home') ? 'bg-slate-900 text-white font-semibold' : 'text-slate-600' }}">Beranda</a>
            <a href="{{ route('reports.create') }}" class="px-3 py-1.5 rounded-lg whitespace-nowrap {{ request()->routeIs('reports.*') ? 'bg-slate-900 text-white font-semibold' : 'text-slate-600' }}">Buat Laporan</a>
            <a href="{{ route('tracking.index') }}" class="px-3 py-1.5 rounded-lg whitespace-nowrap {{ request()->routeIs('tracking.*') ? 'bg-slate-900 text-white font-semibold' : 'text-slate-600' }}">Lacak Tiket</a>
            <a href="{{ route('public.feed') }}" class="px-3 py-1.5 rounded-lg whitespace-nowrap {{ request()->routeIs('public.feed') ? 'bg-slate-900 text-white font-semibold' : 'text-slate-600' }}">Aspirasi Publik</a>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="no-print border-t border-slate-200/80 bg-white mt-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div>
                <x-brand-logo size="sm" subtitle="Sistem Integrasi Layanan Interaksi dan Respon Aspirasi" />
                <p class="text-xs text-slate-500 mt-3 max-w-md leading-relaxed">
                    Program kerja resmi Dewan Perwakilan Mahasiswa (DPM) Fakultas Dakwah dan Ilmu Komunikasi untuk pengelolaan aspirasi yang transparan dan terukur.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-5 text-xs font-medium text-slate-600">
                <a href="{{ route('reports.create') }}" class="hover:text-red-700 transition">Buat Laporan</a>
                <a href="{{ route('tracking.index') }}" class="hover:text-red-700 transition">Lacak Tiket</a>
                <a href="{{ route('public.feed') }}" class="hover:text-red-700 transition">Aspirasi Publik</a>
                <a href="{{ route('admin.login') }}" class="hover:text-red-700 transition">Portal Admin DPM</a>
            </div>
        </div>
    </footer>
</body>
</html>
