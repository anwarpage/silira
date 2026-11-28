<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#ffffff">
    <meta name="description" content="Portal layanan aspirasi dan pengaduan civitas DPM Fakultas Dakwah dan Ilmu Komunikasi.">
    <title>@yield('title', 'Silira — Portal Aspirasi DPM FDIK')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-white text-[#0f172a]">
    <a href="#konten-utama" class="skip-link">Langsung ke konten utama</a>

    {{-- Crisp Silicon Valley SaaS Header --}}
    <header class="no-print sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-[#0f172a]/8">
        <div class="max-w-[1180px] mx-auto px-5 sm:px-8 h-[68px] flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="hover:opacity-85 transition shrink-0">
                <x-brand-logo />
            </a>

            <nav aria-label="Navigasi utama" class="hidden md:flex items-center gap-1 text-[14.5px] font-medium">
                <a
                    href="{{ route('home') }}"
                    @if(request()->routeIs('home')) aria-current="page" @endif
                    class="px-3.5 py-2 rounded-[8px] transition {{ request()->routeIs('home') ? 'bg-[#f8fafc] text-[#0f172a]' : 'text-[#475569] hover:text-[#0f172a] hover:bg-[#f8fafc]/70' }}"
                >Beranda</a>
                <a
                    href="{{ route('public.feed') }}"
                    @if(request()->routeIs('public.feed', 'public.aspirations.*')) aria-current="page" @endif
                    class="px-3.5 py-2 rounded-[8px] transition {{ request()->routeIs('public.feed', 'public.aspirations.*') ? 'bg-[#f8fafc] text-[#0f172a]' : 'text-[#475569] hover:text-[#0f172a] hover:bg-[#f8fafc]/70' }}"
                >Aspirasi</a>
                <a
                    href="{{ route('tracking.index') }}"
                    @if(request()->routeIs('tracking.*')) aria-current="page" @endif
                    class="px-3.5 py-2 rounded-[8px] transition {{ request()->routeIs('tracking.*') ? 'bg-[#f8fafc] text-[#0f172a]' : 'text-[#475569] hover:text-[#0f172a] hover:bg-[#f8fafc]/70' }}"
                >Lacak Tiket</a>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2 rounded-[8px] text-[#1e3a8a] hover:bg-[#eff6ff] transition">
                        Dashboard
                    </a>
                @endauth
            </nav>

            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('reports.create') }}" class="s-btn-primary h-[38px] md:h-[40px] px-4 text-[14px] whitespace-nowrap">
                    Kirim Aspirasi
                </a>

                <button
                    type="button"
                    data-mobile-toggle="publicMobileNav"
                    aria-controls="publicMobileNav"
                    aria-expanded="false"
                    aria-label="Buka menu navigasi"
                    class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-[9px] border border-[#0f172a]/15 bg-white text-[#0f172a] hover:bg-[#f8fafc] transition cursor-pointer"
                >
                    <svg data-icon-open class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                    <svg data-icon-close class="w-5 h-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Navigation Drawer --}}
        <nav id="publicMobileNav" aria-label="Navigasi seluler" class="hidden md:hidden absolute top-full left-0 right-0 z-50 border-t border-b border-[#0f172a]/10 bg-white px-5 py-3 space-y-1 shadow-xl shadow-[#0f172a]/8">
            <a href="{{ route('home') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-[8px] text-[15px] font-medium transition {{ request()->routeIs('home') ? 'bg-[#0f172a] text-white' : 'text-[#0f172a] hover:bg-[#f8fafc]' }}">
                <span>Beranda</span>
            </a>
            <a href="{{ route('public.feed') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-[8px] text-[15px] font-medium transition {{ request()->routeIs('public.feed', 'public.aspirations.*') ? 'bg-[#0f172a] text-white' : 'text-[#0f172a] hover:bg-[#f8fafc]' }}">
                <span>Aspirasi Publik</span>
            </a>
            <a href="{{ route('tracking.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-[8px] text-[15px] font-medium transition {{ request()->routeIs('tracking.*') ? 'bg-[#0f172a] text-white' : 'text-[#0f172a] hover:bg-[#f8fafc]' }}">
                <span>Lacak Tiket</span>
            </a>
            @auth
                <div class="pt-2 mt-2 border-t border-[#0f172a]/10">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-[8px] text-[15px] font-medium text-[#1e3a8a] hover:bg-[#eff6ff] transition">
                        <span>Dashboard Admin</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            @endauth
        </nav>
    </header>

    <main id="konten-utama" class="flex-1">
        @yield('content')
    </main>

    {{-- Clean Minimal SaaS Footer --}}
    <footer class="no-print bg-[#0f172a] text-[#e6edf3]">
        <div class="max-w-[1180px] mx-auto px-5 sm:px-8">
            <div class="py-12 grid grid-cols-1 md:grid-cols-12 gap-10 border-b border-white/10">
                <div class="md:col-span-5">
                    <x-brand-logo :inverted="true" />
                    <p class="mt-3.5 text-[14px] text-[#94a3b8] leading-relaxed max-w-xs">
                        Portal aspirasi dan pengaduan resmi DPM Fakultas Dakwah dan Ilmu Komunikasi.
                    </p>
                </div>

                <div class="md:col-span-7 grid grid-cols-2 sm:grid-cols-3 gap-8 text-[14px]">
                    <div>
                        <h3 class="font-medium text-white mb-3 text-[13.5px]">Layanan</h3>
                        <ul class="space-y-2.5 text-[#94a3b8]">
                            <li><a href="{{ route('reports.create') }}" class="hover:text-white transition">Kirim Aspirasi</a></li>
                            <li><a href="{{ route('tracking.index') }}" class="hover:text-white transition">Lacak Tiket</a></li>
                            <li><a href="{{ route('public.feed') }}" class="hover:text-white transition">Aspirasi Publik</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-medium text-white mb-3 text-[13.5px]">Privasi</h3>
                        <ul class="space-y-2.5 text-[#94a3b8]">
                            <li><a href="{{ route('public.feed') }}" class="hover:text-white transition">Mode Publik</a></li>
                            <li><a href="{{ route('reports.create') }}" class="hover:text-white transition">Mode Anonim</a></li>
                            <li><a href="{{ route('reports.create') }}" class="hover:text-white transition">Mode Rahasia</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="font-medium text-white mb-3 text-[13.5px]">Pengurus</h3>
                        <ul class="space-y-2.5 text-[#94a3b8]">
                            @auth
                                <li><a href="{{ route('admin.dashboard') }}" class="hover:text-white transition">Dashboard Admin</a></li>
                                <li><a href="{{ route('admin.reports.index') }}" class="hover:text-white transition">Kelola Laporan</a></li>
                            @else
                                <li><a href="{{ route('admin.login') }}" class="hover:text-white transition">Portal Admin</a></li>
                            @endauth
                        </ul>
                    </div>
                </div>
            </div>

            <div class="py-5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[13px] text-[#94a3b8]">
                <span>&copy; {{ date('Y') }} Silira &middot; DPM FDIK</span>
                <span>Sistem Layanan Aspirasi Terpadu</span>
            </div>
        </div>
    </footer>
</body>
</html>
