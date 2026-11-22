<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin DPM — SILIRA')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|jetbrains-mono:500,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FAFAFA] text-slate-900 antialiased min-h-screen flex">
    @php
        $pendingCount = \App\Models\Report::where('status', 'menunggu')->count();
    @endphp

    {{-- Sidebar --}}
    <aside class="hidden lg:flex lg:flex-col w-64 bg-slate-950 text-slate-300 shrink-0 border-r border-slate-900">
        <div class="h-16 px-5 flex items-center border-b border-slate-800/80">
            <a href="{{ route('admin.dashboard') }}" class="hover:opacity-90 transition">
                <x-brand-logo variant="dark" size="sm" subtitle="Admin DPM FDIK" />
            </a>
        </div>

        <nav class="flex-1 p-3.5 space-y-1 text-sm font-medium">
            <p class="px-3 pt-2 pb-1 text-[10px] font-bold uppercase tracking-widest text-slate-500">Utama</p>

            <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white font-semibold' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.reports.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.reports.*') ? 'bg-white/10 text-white font-semibold' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                <span>Laporan Masuk</span>
                @if ($pendingCount > 0)
                    <span class="font-mono text-[11px] font-bold px-2 py-0.5 rounded-full bg-red-600 text-white tabular-nums">{{ $pendingCount }}</span>
                @endif
            </a>

            <p class="px-3 pt-5 pb-1 text-[10px] font-bold uppercase tracking-widest text-slate-500">Pengaturan</p>

            <a href="{{ route('admin.categories.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.categories.*') ? 'bg-white/10 text-white font-semibold' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                <span>Kategori Bidang</span>
            </a>

            <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl transition {{ request()->routeIs('admin.users.*') ? 'bg-white/10 text-white font-semibold' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">
                <span>Akun Pengurus</span>
            </a>

            <div class="pt-5 mt-5 border-t border-slate-800/80">
                <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-between px-3 py-2 rounded-xl text-slate-400 hover:bg-white/5 hover:text-white transition text-xs">
                    <span>Portal Publik SILIRA</span>
                    <span>&nearr;</span>
                </a>
            </div>
        </nav>

        <div class="p-4 border-t border-slate-800/80 bg-slate-900/40">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-slate-800 text-slate-200 flex items-center justify-center text-xs font-bold shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-slate-400 truncate">{{ auth()->user()->jabatan ?? auth()->user()->email }}</p>
                </div>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="btn-press w-full px-3 py-2 rounded-lg bg-slate-800/90 text-slate-200 hover:bg-slate-700 text-xs font-semibold cursor-pointer">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Wrapper --}}
    <div class="flex-1 flex flex-col min-w-0">
        {{-- Mobile Topbar --}}
        <header class="lg:hidden bg-slate-950 text-white px-4 py-3 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}">
                <x-brand-logo variant="dark" size="sm" />
            </a>
            <div class="flex items-center gap-3 text-xs font-medium">
                <a href="{{ route('admin.dashboard') }}" class="hover:underline">Dashboard</a>
                <a href="{{ route('admin.reports.index') }}" class="hover:underline">Laporan</a>
                <a href="{{ route('admin.categories.index') }}" class="hover:underline">Kategori</a>
                <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-red-400 font-semibold">Keluar</button>
                </form>
            </div>
        </header>

        <main class="flex-1 p-5 sm:p-8 max-w-7xl w-full mx-auto">
            @if (session('success'))
                <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3.5 text-sm font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 px-4 py-3.5 text-sm font-medium text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
