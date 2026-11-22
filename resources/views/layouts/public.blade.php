<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SILIRA — Layanan Aspirasi & Pengaduan DPM FDIK')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
        @media print { .no-print { display: none !important; } }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex flex-col">
    <header class="no-print sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-9 h-9 rounded-lg bg-red-700 text-white flex items-center justify-center font-extrabold text-sm tracking-tight shadow-xs group-hover:bg-red-800 transition">
                    SL
                </div>
                <div class="leading-tight">
                    <span class="block font-extrabold text-base tracking-tight text-slate-900">SILIRA</span>
                    <span class="block text-[11px] font-medium text-slate-500">DPM Fakultas Dakwah &amp; Ilmu Komunikasi</span>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-1 text-sm font-medium">
                <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('home') ? 'bg-red-50 text-red-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Beranda</a>
                <a href="{{ route('reports.create') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('reports.*') ? 'bg-red-50 text-red-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Buat Laporan</a>
                <a href="{{ route('tracking.index') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('tracking.*') ? 'bg-red-50 text-red-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Lacak Tiket</a>
                <a href="{{ route('public.feed') }}" class="px-3 py-2 rounded-lg transition {{ request()->routeIs('public.feed') ? 'bg-red-50 text-red-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">Aspirasi Publik</a>
            </nav>

            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-900 text-white hover:bg-slate-800 transition">
                        Dashboard DPM
                    </a>
                @else
                    <a href="{{ route('admin.login') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold border border-slate-300 text-slate-700 hover:bg-slate-100 transition">
                        Login Admin
                    </a>
                @endauth
            </div>
        </div>

        {{-- Mobile quick nav --}}
        <div class="md:hidden border-t border-slate-100 px-4 py-2 flex items-center gap-2 overflow-x-auto text-xs font-medium">
            <a href="{{ route('home') }}" class="px-2.5 py-1.5 rounded-md whitespace-nowrap {{ request()->routeIs('home') ? 'bg-red-50 text-red-700 font-semibold' : 'text-slate-600' }}">Beranda</a>
            <a href="{{ route('reports.create') }}" class="px-2.5 py-1.5 rounded-md whitespace-nowrap {{ request()->routeIs('reports.*') ? 'bg-red-50 text-red-700 font-semibold' : 'text-slate-600' }}">Buat Laporan</a>
            <a href="{{ route('tracking.index') }}" class="px-2.5 py-1.5 rounded-md whitespace-nowrap {{ request()->routeIs('tracking.*') ? 'bg-red-50 text-red-700 font-semibold' : 'text-slate-600' }}">Lacak Tiket</a>
            <a href="{{ route('public.feed') }}" class="px-2.5 py-1.5 rounded-md whitespace-nowrap {{ request()->routeIs('public.feed') ? 'bg-red-50 text-red-700 font-semibold' : 'text-slate-600' }}">Aspirasi Publik</a>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="no-print border-t border-slate-200 bg-white mt-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-sm text-slate-500">
            <div>
                <p class="font-bold text-slate-800">SILIRA — Sistem Integrasi Layanan Interaksi dan Respon Aspirasi</p>
                <p class="text-xs mt-1">Program Kerja Dewan Perwakilan Mahasiswa (DPM) Fakultas Dakwah dan Ilmu Komunikasi.</p>
            </div>
            <div class="flex items-center gap-4 text-xs">
                <a href="{{ route('reports.create') }}" class="hover:text-red-700 transition">Kirim Aspirasi</a>
                <a href="{{ route('tracking.index') }}" class="hover:text-red-700 transition">Lacak Tiket</a>
                <a href="{{ route('admin.login') }}" class="hover:text-red-700 transition">Portal Admin DPM</a>
            </div>
        </div>
    </footer>
</body>
</html>
