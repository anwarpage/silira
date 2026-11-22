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
<body class="min-h-screen flex bg-[#e5e1d8] text-[#2a1d17]">
    @php
        $pendingCount = \App\Models\Report::where('status', 'menunggu')->count();
    @endphp

    {{-- Sidebar on --p-surface (#dad5ca) --}}
    <aside class="hidden lg:flex lg:flex-col w-64 bg-[#dad5ca] border-r border-[#2a1d17]/13 shrink-0">
        <div class="h-[72px] px-6 flex items-center justify-between border-b border-[#2a1d17]/12">
            <a href="{{ route('admin.dashboard') }}" class="hover:opacity-80 transition">
                <x-brand-logo />
            </a>
            <span class="text-[13px] text-[#65564c]">Admin</span>
        </div>

        <nav class="flex-1 p-4 space-y-1 text-[15px] font-medium">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-[8px] transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#2a1d17] text-[#e5e1d8]' : 'text-[#2a1d17]/80 hover:bg-[#2a1d17]/6 hover:text-[#2a1d17]' }}">
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.reports.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-[8px] transition {{ request()->routeIs('admin.reports.*') ? 'bg-[#2a1d17] text-[#e5e1d8]' : 'text-[#2a1d17]/80 hover:bg-[#2a1d17]/6 hover:text-[#2a1d17]' }}">
                <span>Laporan masuk</span>
                @if ($pendingCount > 0)
                    <span class="font-mono text-[13px] px-2 py-0.5 rounded-[6px] {{ request()->routeIs('admin.reports.*') ? 'bg-[#456f3a] text-white' : 'bg-[#efe6d0] text-[#6d4c14]' }}">{{ $pendingCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.categories.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-[8px] transition {{ request()->routeIs('admin.categories.*') ? 'bg-[#2a1d17] text-[#e5e1d8]' : 'text-[#2a1d17]/80 hover:bg-[#2a1d17]/6 hover:text-[#2a1d17]' }}">
                <span>Kategori bidang</span>
            </a>

            <a href="{{ route('admin.users.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-[8px] transition {{ request()->routeIs('admin.users.*') ? 'bg-[#2a1d17] text-[#e5e1d8]' : 'text-[#2a1d17]/80 hover:bg-[#2a1d17]/6 hover:text-[#2a1d17]' }}">
                <span>Akun pengurus</span>
            </a>

            <div class="pt-4 mt-4 border-t border-[#2a1d17]/12">
                <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2 rounded-[8px] text-[14px] text-[#65564c] hover:bg-[#2a1d17]/6 hover:text-[#2a1d17] transition">
                    <span>Portal publik</span>
                    <span>&nearr;</span>
                </a>
            </div>
        </nav>

        <div class="p-4 border-t border-[#2a1d17]/12">
            <p class="text-[14px] font-medium text-[#2a1d17] truncate">{{ auth()->user()->name }}</p>
            <p class="text-[13px] text-[#65564c] truncate">{{ auth()->user()->jabatan ?? auth()->user()->email }}</p>
            <form action="{{ route('admin.logout') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="s-btn-secondary w-full h-[40px] text-[14px]">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="lg:hidden bg-[#dad5ca] border-b border-[#2a1d17]/13 px-5 py-3 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}">
                <x-brand-logo size="sm" />
            </a>
            <div class="flex items-center gap-4 text-[14px] font-medium">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a href="{{ route('admin.reports.index') }}">Laporan</a>
                <a href="{{ route('admin.categories.index') }}">Kategori</a>
                <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-[#7a2824]">Keluar</button>
                </form>
            </div>
        </header>

        <main class="flex-1 p-6 sm:p-10 max-w-6xl w-full mx-auto">
            @if (session('success'))
                <div class="mb-6 rounded-[10px] bg-[#d9e7d6] border border-[#456f3a]/30 px-4 py-3 text-[14px] font-medium text-[#284b20]">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-[10px] bg-[#eedcdb] border border-[#9b3a34]/30 px-4 py-3 text-[14px] font-medium text-[#7a2824]">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
