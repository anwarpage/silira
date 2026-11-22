<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'Admin — Silira')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Onest:wght@400..600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex bg-[#f5f7fa] text-[#0f172a]">
    @php
        $pendingCount = \App\Models\Report::where('status', 'menunggu')->count();
    @endphp

    {{-- Sidebar on --p-surface (#eceff4) --}}
    <aside class="hidden lg:flex lg:flex-col w-64 bg-[#eceff4] border-r border-[#0f172a]/13 shrink-0">
        <div class="h-[72px] px-6 flex items-center justify-between border-b border-[#0f172a]/12">
            <a href="{{ route('admin.dashboard') }}" class="hover:opacity-80 transition">
                <x-brand-logo />
            </a>
            <span class="text-[13px] text-[#475569]">Admin</span>
        </div>

        <nav class="flex-1 p-4 space-y-1 text-[15px] font-medium">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-[8px] transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#0f172a] text-[#f5f7fa]' : 'text-[#0f172a]/80 hover:bg-[#0f172a]/6 hover:text-[#0f172a]' }}">
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.reports.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-[8px] transition {{ request()->routeIs('admin.reports.*') ? 'bg-[#0f172a] text-[#f5f7fa]' : 'text-[#0f172a]/80 hover:bg-[#0f172a]/6 hover:text-[#0f172a]' }}">
                <span>Laporan masuk</span>
                @if ($pendingCount > 0)
                    <span class="font-mono text-[13px] px-2 py-0.5 rounded-[6px] {{ request()->routeIs('admin.reports.*') ? 'bg-[#1e3a8a] text-white' : 'bg-[#fef3c7] text-[#92400e]' }}">{{ $pendingCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.categories.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-[8px] transition {{ request()->routeIs('admin.categories.*') ? 'bg-[#0f172a] text-[#f5f7fa]' : 'text-[#0f172a]/80 hover:bg-[#0f172a]/6 hover:text-[#0f172a]' }}">
                <span>Kategori bidang</span>
            </a>

            <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-[8px] transition {{ request()->routeIs('admin.users.*') ? 'bg-[#0f172a] text-[#f5f7fa]' : 'text-[#0f172a]/80 hover:bg-[#0f172a]/6 hover:text-[#0f172a]' }}">
                <span>Akun pengurus</span>
            </a>

            <div class="pt-4 mt-4 border-t border-[#0f172a]/12">
                <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2 rounded-[8px] text-[14px] text-[#475569] hover:bg-[#0f172a]/6 hover:text-[#0f172a] transition">
                    <span>Portal publik</span>
                    <span>&nearr;</span>
                </a>
            </div>
        </nav>

        <div class="p-4 border-t border-[#0f172a]/12">
            <p class="text-[14px] font-medium text-[#0f172a] truncate">{{ auth()->user()->name }}</p>
            <p class="text-[13px] text-[#475569] truncate">{{ auth()->user()->jabatan ?? auth()->user()->email }}</p>
            <form action="{{ route('admin.logout') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="s-btn-secondary w-full h-[40px] text-[14px]">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="lg:hidden bg-[#eceff4] border-b border-[#0f172a]/13 px-5 py-3 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}">
                <x-brand-logo size="sm" />
            </a>
            <div class="flex items-center gap-3.5 text-[14px] font-medium">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.reports.index') }}">Laporan</a>
                <a href="{{ route('admin.categories.index') }}">Kategori</a>
                <a href="{{ route('admin.users.index') }}">Akun</a>
                <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-[#991b1b]">Keluar</button>
                </form>
            </div>
        </header>

        <main class="flex-1 p-6 sm:p-10 max-w-6xl w-full mx-auto">
            @if (session('success'))
                <div class="mb-6 rounded-[10px] bg-[#eff4ff] border border-[#1e3a8a]/30 px-4 py-3 text-[14px] font-medium text-[#1e3a8a]">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-[10px] bg-[#fee2e2] border border-[#dc2626]/30 px-4 py-3 text-[14px] font-medium text-[#991b1b]">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
