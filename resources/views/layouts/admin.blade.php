<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin DPM — SILIRA')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex">
    {{-- Sidebar --}}
    <aside class="hidden lg:flex lg:flex-col w-64 bg-slate-900 text-slate-200 shrink-0">
        <div class="h-16 px-6 flex items-center gap-3 border-b border-slate-800">
            <div class="w-8 h-8 rounded-lg bg-red-600 text-white flex items-center justify-center font-extrabold text-xs">SL</div>
            <div class="leading-tight">
                <span class="block font-extrabold text-sm text-white">SILIRA Admin</span>
                <span class="block text-[11px] text-slate-400">DPM FDIK</span>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-1 text-sm font-medium">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-red-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                Dashboard
            </a>
            <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.reports.*') ? 'bg-red-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                Laporan Masuk
            </a>
            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.categories.*') ? 'bg-red-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                Kategori Bidang
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl transition {{ request()->routeIs('admin.users.*') ? 'bg-red-700 text-white font-semibold' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                Akun Pengurus
            </a>
            <div class="pt-4 mt-4 border-t border-slate-800">
                <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition text-xs">
                    Buka Portal Publik &nearr;
                </a>
            </div>
        </nav>

        <div class="p-4 border-t border-slate-800 text-xs">
            <p class="font-semibold text-white truncate">{{ auth()->user()->name }}</p>
            <p class="text-slate-400 truncate">{{ auth()->user()->jabatan ?? auth()->user()->email }}</p>
            <form action="{{ route('admin.logout') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit" class="w-full px-3 py-2 rounded-lg bg-slate-800 text-slate-200 hover:bg-slate-700 font-semibold transition cursor-pointer">
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Wrapper --}}
    <div class="flex-1 flex flex-col min-w-0">
        {{-- Mobile Topbar --}}
        <header class="lg:hidden bg-slate-900 text-white px-4 py-3 flex items-center justify-between">
            <a href="{{ route('admin.dashboard') }}" class="font-extrabold text-sm">SILIRA Admin</a>
            <div class="flex items-center gap-3 text-xs">
                <a href="{{ route('admin.dashboard') }}" class="hover:underline">Dashboard</a>
                <a href="{{ route('admin.reports.index') }}" class="hover:underline">Laporan</a>
                <a href="{{ route('admin.categories.index') }}" class="hover:underline">Kategori</a>
                <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-red-300 font-semibold">Keluar</button>
                </form>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-8 max-w-7xl w-full mx-auto">
            @if (session('success'))
                <div class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm font-medium text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</body>
</html>
