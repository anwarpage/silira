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
    <header class="no-print sticky top-0 z-40 h-[72px] bg-[#e5e1d8] border-b border-[#2a1d17]/13">
        <div class="max-w-[1200px] mx-auto px-6 sm:px-10 h-full flex items-center justify-between gap-6">
            <a href="{{ route('home') }}" class="hover:opacity-80 transition">
                <x-brand-logo />
            </a>

            <nav class="hidden md:flex items-center gap-7 text-[15px] font-medium">
                <a href="{{ route('home') }}" class="py-1 transition {{ request()->routeIs('home') ? 'text-[#2a1d17] border-b-2 border-[#2a1d17]' : 'text-[#65564c] hover:text-[#2a1d17]' }}">Beranda</a>
                <a href="{{ route('public.feed') }}" class="py-1 transition {{ request()->routeIs('public.feed') ? 'text-[#2a1d17] border-b-2 border-[#2a1d17]' : 'text-[#65564c] hover:text-[#2a1d17]' }}">Aspirasi</a>
                <a href="{{ route('tracking.index') }}" class="py-1 transition {{ request()->routeIs('tracking.*') ? 'text-[#2a1d17] border-b-2 border-[#2a1d17]' : 'text-[#65564c] hover:text-[#2a1d17]' }}">Lacak</a>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="py-1 text-[#65564c] hover:text-[#2a1d17] transition">Dashboard</a>
                @else
                    <a href="{{ route('admin.login') }}" class="py-1 text-[#65564c] hover:text-[#2a1d17] transition">Admin</a>
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
    <div class="no-print md:hidden bg-[#e5e1d8] border-b border-[#2a1d17]/12 px-6 py-2.5 flex items-center gap-5 overflow-x-auto text-[14px] font-medium">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-[#2a1d17] underline underline-offset-4' : 'text-[#65564c]' }}">Beranda</a>
        <a href="{{ route('public.feed') }}" class="{{ request()->routeIs('public.feed') ? 'text-[#2a1d17] underline underline-offset-4' : 'text-[#65564c]' }}">Aspirasi</a>
        <a href="{{ route('tracking.index') }}" class="{{ request()->routeIs('tracking.*') ? 'text-[#2a1d17] underline underline-offset-4' : 'text-[#65564c]' }}">Lacak</a>
        <a href="{{ auth()->check() ? route('admin.dashboard') : route('admin.login') }}" class="text-[#65564c]">{{ auth()->check() ? 'Dashboard' : 'Admin' }}</a>
    </div>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="no-print bg-[#2a1d17] text-[#e5e1d8] mt-20">
        <div class="max-w-[1200px] mx-auto px-6 sm:px-10 py-14">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 pb-10 border-b border-[#e5e1d8]/15">
                <div class="max-w-md">
                    <x-brand-logo :inverted="true" />
                    <p class="mt-4 text-[15px] text-[#a89f96] leading-relaxed">
                        Sistem Integrasi Layanan Interaksi dan Respon Aspirasi — Dewan Perwakilan Mahasiswa Fakultas Dakwah dan Ilmu Komunikasi.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-6 text-[15px] font-medium">
                    <a href="{{ route('reports.create') }}" class="hover:text-white transition">Kirim aspirasi</a>
                    <a href="{{ route('tracking.index') }}" class="hover:text-white transition">Lacak tiket</a>
                    <a href="{{ route('public.feed') }}" class="hover:text-white transition">Aspirasi publik</a>
                    <a href="{{ route('admin.login') }}" class="hover:text-white transition">Portal admin</a>
                </div>
            </div>

            <div class="pt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[13px] text-[#a89f96]">
                <span>Silira &middot; DPM Fakultas Dakwah dan Ilmu Komunikasi</span>
                <span>{{ date('Y') }}</span>
            </div>
        </div>
    </footer>
</body>
</html>
