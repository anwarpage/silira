<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SILIRA — Portal Aspirasi DPM FDIK')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F7F7F5] text-stone-900 antialiased min-h-screen flex flex-col">
    <header class="no-print sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-stone-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-14 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="hover:opacity-85 transition">
                <x-brand-logo size="sm" subtitle="DPM FDIK" />
            </a>

            <nav class="hidden md:flex items-center gap-1 text-[13px] font-medium">
                <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-md transition {{ request()->routeIs('home') ? 'bg-stone-100 text-stone-950 font-semibold' : 'text-stone-600 hover:text-stone-950 hover:bg-stone-50' }}">Beranda</a>
                <a href="{{ route('reports.create') }}" class="px-3 py-1.5 rounded-md transition {{ request()->routeIs('reports.*') ? 'bg-stone-100 text-stone-950 font-semibold' : 'text-stone-600 hover:text-stone-950 hover:bg-stone-50' }}">Buat Laporan</a>
                <a href="{{ route('tracking.index') }}" class="px-3 py-1.5 rounded-md transition {{ request()->routeIs('tracking.*') ? 'bg-stone-100 text-stone-950 font-semibold' : 'text-stone-600 hover:text-stone-950 hover:bg-stone-50' }}">Lacak Tiket</a>
                <a href="{{ route('public.feed') }}" class="px-3 py-1.5 rounded-md transition {{ request()->routeIs('public.feed') ? 'bg-stone-100 text-stone-950 font-semibold' : 'text-stone-600 hover:text-stone-950 hover:bg-stone-50' }}">Aspirasi Publik</a>
            </nav>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="btn-press px-3 py-1.5 rounded-lg text-xs font-semibold bg-stone-900 text-white hover:bg-stone-800">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('admin.login') }}" class="btn-press px-3 py-1.5 rounded-lg text-xs font-medium border border-stone-300 bg-white text-stone-700 hover:bg-stone-50">
                        Admin
                    </a>
                @endauth
            </div>
        </div>

        <div class="md:hidden border-t border-stone-100 px-4 py-1.5 flex items-center gap-1 overflow-x-auto text-xs font-medium bg-white">
            <a href="{{ route('home') }}" class="px-2.5 py-1 rounded-md whitespace-nowrap {{ request()->routeIs('home') ? 'bg-stone-100 text-stone-950 font-semibold' : 'text-stone-600' }}">Beranda</a>
            <a href="{{ route('reports.create') }}" class="px-2.5 py-1 rounded-md whitespace-nowrap {{ request()->routeIs('reports.*') ? 'bg-stone-100 text-stone-950 font-semibold' : 'text-stone-600' }}">Buat Laporan</a>
            <a href="{{ route('tracking.index') }}" class="px-2.5 py-1 rounded-md whitespace-nowrap {{ request()->routeIs('tracking.*') ? 'bg-stone-100 text-stone-950 font-semibold' : 'text-stone-600' }}">Lacak Tiket</a>
            <a href="{{ route('public.feed') }}" class="px-2.5 py-1 rounded-md whitespace-nowrap {{ request()->routeIs('public.feed') ? 'bg-stone-100 text-stone-950 font-semibold' : 'text-stone-600' }}">Aspirasi</a>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="no-print border-t border-stone-200 bg-white mt-16">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-xs text-stone-500">
            <div class="flex items-center gap-2.5">
                <x-brand-logo size="sm" :markOnly="true" />
                <span><strong class="text-stone-800 font-semibold">SILIRA</strong> &middot; Dewan Perwakilan Mahasiswa Fakultas Dakwah dan Ilmu Komunikasi</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('reports.create') }}" class="hover:text-stone-900">Buat Laporan</a>
                <a href="{{ route('tracking.index') }}" class="hover:text-stone-900">Lacak Tiket</a>
                <a href="{{ route('admin.login') }}" class="hover:text-stone-900">Admin DPM</a>
            </div>
        </div>
    </footer>
</body>
</html>
