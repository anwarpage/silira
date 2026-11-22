<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin — SILIRA')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F7F7F5] text-stone-900 antialiased min-h-screen flex">
    @php
        $pendingCount = \App\Models\Report::where('status', 'menunggu')->count();
    @endphp

    {{-- Clean Light Workspace Sidebar --}}
    <aside class="hidden lg:flex lg:flex-col w-60 bg-white border-r border-stone-200 shrink-0">
        <div class="h-14 px-5 flex items-center border-b border-stone-200">
            <a href="{{ route('admin.dashboard') }}" class="hover:opacity-85 transition">
                <x-brand-logo size="sm" subtitle="Admin DPM" />
            </a>
        </div>

        <nav class="flex-1 p-3 space-y-0.5 text-[13px] font-medium">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-stone-900 text-white font-semibold' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-950' }}">
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.reports.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.reports.*') ? 'bg-stone-900 text-white font-semibold' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-950' }}">
                <span>Laporan Masuk</span>
                @if ($pendingCount > 0)
                    <span class="font-mono text-[11px] font-semibold px-1.5 py-0.5 rounded {{ request()->routeIs('admin.reports.*') ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-900' }}">{{ $pendingCount }}</span>
                @endif
            </a>

            <div class="pt-4 pb-1 px-3 text-[10px] font-semibold uppercase tracking-wider text-stone-400">Kelola</div>

            <a href="{{ route('admin.categories.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.categories.*') ? 'bg-stone-900 text-white font-semibold' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-950' }}">
                <span>Kategori Bidang</span>
            </a>

            <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('admin.users.*') ? 'bg-stone-900 text-white font-semibold' : 'text-stone-600 hover:bg-stone-100 hover:text-stone-950' }}">
                <span>Akun Pengurus</span>
            </a>

            <div class="pt-4 mt-4 border-t border-stone-100">
                <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-between px-3 py-2 rounded-lg text-stone-500 hover:bg-stone-100 hover:text-stone-900 transition text-xs">
                    <span>Portal Publik</span>
                    <span>&nearr;</span>
                </a>
            </div>
        </nav>

        <div class="p-3.5 border-t border-stone-200 bg-stone-50/60">
            <p class="text-xs font-semibold text-stone-900 truncate">{{ auth()->user()->name }}</p>
            <p class="text-[11px] text-stone-500 truncate">{{ auth()->user()->jabatan ?? auth()->user()->email }}</p>
            <form action="{{ route('admin.logout') }}" method="POST" class="mt-2.5">
                @csrf
                <button type="submit" class="btn-press w-full px-3 py-1.5 rounded-lg border border-stone-300 bg-white text-stone-700 hover:bg-stone-100 text-xs font-medium cursor-pointer">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Wrapper --}}
    <div class="flex-1 flex flex-col min-w-0">
        <header class="lg:hidden bg-white border-b border-stone-200 px-4 py-3 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}">
                <x-brand-logo size="sm" />
            </a>
            <div class="flex items-center gap-3 text-xs font-medium text-stone-700">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.reports.index') }}">Laporan</a>
                <a href="{{ route('admin.categories.index') }}">Kategori</a>
                <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-rose-700 font-semibold">Keluar</button>
                </form>
            </div>
        </header>

        <main class="flex-1 p-5 sm:p-8 max-w-6xl w-full mx-auto">
            @if (session('success'))
                <div class="mb-5 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-xs font-medium text-emerald-900">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-5 rounded-lg bg-rose-50 border border-rose-200 px-4 py-3 text-xs font-medium text-rose-900">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
