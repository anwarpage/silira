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
    <header class="no-print sticky top-0 z-40 h-[72px] bg-[#f5f7fa] border-b border-[#0f172a]/13">
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
    <div class="no-print md:hidden bg-[#f5f7fa] border-b border-[#0f172a]/12 px-6 py-2.5 flex items-center gap-5 overflow-x-auto text-[14px] font-medium">
        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-[#0f172a] underline underline-offset-4' : 'text-[#475569]' }}">Beranda</a>
        <a href="{{ route('public.feed') }}" class="{{ request()->routeIs('public.feed') ? 'text-[#0f172a] underline underline-offset-4' : 'text-[#475569]' }}">Aspirasi</a>
        <a href="{{ route('tracking.index') }}" class="{{ request()->routeIs('tracking.*') ? 'text-[#0f172a] underline underline-offset-4' : 'text-[#475569]' }}">Lacak</a>
        <a href="{{ auth()->check() ? route('admin.dashboard') : route('admin.login') }}" class="text-[#475569]">{{ auth()->check() ? 'Dashboard' : 'Admin' }}</a>
    </div>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="no-print bg-[#0f172a] text-[#f5f7fa] mt-20">
        <div class="max-w-[1200px] mx-auto px-6 sm:px-10 py-14">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 pb-10 border-b border-[#f5f7fa]/15">
                <div class="max-w-md">
                    <x-brand-logo :inverted="true" />
                    <p class="mt-4 text-[15px] text-[#94a3b8] leading-relaxed">
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

            <div class="pt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[13px] text-[#94a3b8]">
                <span>Silira &middot; DPM Fakultas Dakwah dan Ilmu Komunikasi</span>
                <span>{{ date('Y') }}</span>
            </div>
        </div>
    </footer>
</body>
</html>
