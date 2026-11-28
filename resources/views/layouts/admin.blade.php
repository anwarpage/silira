<!DOCTYPE html>
<html lang="id" class="bg-[#f8fafc]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <title>@yield('title', 'Admin — Silira')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f8fafc] text-[#0f172a] antialiased">
    @php
        $user = auth()->user();
        $initials = collect(explode(' ', trim($user->name)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(mb_substr($part, 0, 1)))
            ->implode('');

        $currentSection = match (true) {
            request()->routeIs('admin.dashboard') => 'Dashboard',
            request()->routeIs('admin.reports.*') => 'Laporan Masuk',
            request()->routeIs('admin.categories.*') => 'Kategori Bidang',
            request()->routeIs('admin.users.*') => 'Akun Pengurus',
            default => 'Panel Admin',
        };
    @endphp

    {{-- Fixed Full-Height Desktop Sidebar (independent from main content scroll & grid) --}}
    <aside style="view-transition-name: admin-sidebar;" class="hidden lg:fixed lg:inset-y-0 lg:left-0 lg:z-40 lg:flex lg:w-[268px] lg:flex-col bg-white border-r border-[#0f172a]/8">
        {{-- Sidebar Brand Header --}}
        <div class="h-16 px-5 flex items-center justify-between border-b border-[#0f172a]/8 shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="hover:opacity-85 transition min-w-0">
                <x-brand-logo size="sm" subtitle="DPM FDIK" />
            </a>
            <span class="text-[11px] font-semibold uppercase tracking-[0.06em] px-2 py-0.5 rounded-[6px] bg-[#eff6ff] text-[#1e3a8a] shrink-0">
                Admin
            </span>
        </div>

        {{-- Scrollable Navigation Groups --}}
        <nav aria-label="Navigasi admin" class="flex-1 overflow-y-auto px-3.5 py-5 space-y-6">
            <div>
                <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-[0.08em] text-[#94a3b8]">
                    Operasional
                </p>
                <div class="space-y-1 text-[14px]">
                    <a
                        href="{{ route('admin.dashboard') }}"
                        @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif
                        class="flex items-center justify-between px-3 py-2.5 rounded-[9px] transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#eff6ff] text-[#1e3a8a] font-semibold' : 'text-[#475569] font-medium hover:bg-[#f8fafc] hover:text-[#0f172a]' }}"
                    >
                        <span class="inline-flex items-center gap-2.5">
                            <svg class="w-[18px] h-[18px] shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-[#1e3a8a]' : 'text-[#64748b]' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                            </svg>
                            <span>Dashboard</span>
                        </span>
                    </a>

                    <a
                        href="{{ route('admin.reports.index') }}"
                        @if(request()->routeIs('admin.reports.*')) aria-current="page" @endif
                        class="flex items-center justify-between px-3 py-2.5 rounded-[9px] transition {{ request()->routeIs('admin.reports.*') ? 'bg-[#eff6ff] text-[#1e3a8a] font-semibold' : 'text-[#475569] font-medium hover:bg-[#f8fafc] hover:text-[#0f172a]' }}"
                    >
                        <span class="inline-flex items-center gap-2.5">
                            <svg class="w-[18px] h-[18px] shrink-0 {{ request()->routeIs('admin.reports.*') ? 'text-[#1e3a8a]' : 'text-[#64748b]' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z" />
                            </svg>
                            <span>Laporan Masuk</span>
                        </span>
                        @if ($pendingCount > 0)
                            <span class="font-mono text-[11.5px] font-semibold px-2 py-0.5 rounded-full {{ request()->routeIs('admin.reports.*') ? 'bg-[#1e3a8a] text-white' : 'bg-[#fef3c7] text-[#92400e]' }}">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </div>
            </div>

            <div>
                <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-[0.08em] text-[#94a3b8]">
                    Pengaturan
                </p>
                <div class="space-y-1 text-[14px]">
                    <a
                        href="{{ route('admin.categories.index') }}"
                        @if(request()->routeIs('admin.categories.*')) aria-current="page" @endif
                        class="flex items-center justify-between px-3 py-2.5 rounded-[9px] transition {{ request()->routeIs('admin.categories.*') ? 'bg-[#eff6ff] text-[#1e3a8a] font-semibold' : 'text-[#475569] font-medium hover:bg-[#f8fafc] hover:text-[#0f172a]' }}"
                    >
                        <span class="inline-flex items-center gap-2.5">
                            <svg class="w-[18px] h-[18px] shrink-0 {{ request()->routeIs('admin.categories.*') ? 'text-[#1e3a8a]' : 'text-[#64748b]' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                            </svg>
                            <span>Kategori Bidang</span>
                        </span>
                    </a>

                    <a
                        href="{{ route('admin.users.index') }}"
                        @if(request()->routeIs('admin.users.*')) aria-current="page" @endif
                        class="flex items-center justify-between px-3 py-2.5 rounded-[9px] transition {{ request()->routeIs('admin.users.*') ? 'bg-[#eff6ff] text-[#1e3a8a] font-semibold' : 'text-[#475569] font-medium hover:bg-[#f8fafc] hover:text-[#0f172a]' }}"
                    >
                        <span class="inline-flex items-center gap-2.5">
                            <svg class="w-[18px] h-[18px] shrink-0 {{ request()->routeIs('admin.users.*') ? 'text-[#1e3a8a]' : 'text-[#64748b]' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                            <span>Akun Pengurus</span>
                        </span>
                    </a>
                </div>
            </div>

            <div>
                <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-[0.08em] text-[#94a3b8]">
                    Portal
                </p>
                <div class="space-y-1 text-[14px]">
                    <a
                        href="{{ route('home') }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-between px-3 py-2.5 rounded-[9px] font-medium text-[#475569] hover:bg-[#f8fafc] hover:text-[#0f172a] transition"
                    >
                        <span class="inline-flex items-center gap-2.5">
                            <svg class="w-[18px] h-[18px] shrink-0 text-[#64748b]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                            </svg>
                            <span>Portal Publik</span>
                        </span>
                        <span class="text-[#94a3b8]" aria-hidden="true">&nearr;</span>
                    </a>
                </div>
            </div>
        </nav>

        {{-- Pinned Sidebar Account Footer --}}
        <div class="p-3.5 border-t border-[#0f172a]/8 bg-[#f8fafc]/60 shrink-0">
            <div class="flex items-center gap-3 px-1">
                <div class="w-9 h-9 rounded-full bg-[#eff6ff] border border-[#1e3a8a]/15 text-[#1e3a8a] flex items-center justify-center font-semibold text-[13px] shrink-0">
                    {{ $initials ?: 'AD' }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-[13.5px] font-semibold text-[#0f172a] truncate">{{ $user->name }}</p>
                    <p class="text-[12px] text-[#475569] truncate">{{ $user->jabatan ?? $user->email }}</p>
                </div>
            </div>

            <form action="{{ route('admin.logout') }}" method="POST" class="mt-3" data-no-loading="1">
                @csrf
                <button type="submit" class="w-full h-[36px] px-3 rounded-[9px] border border-[#0f172a]/12 bg-white hover:bg-[#fee2e2]/60 hover:border-[#dc2626]/30 hover:text-[#991b1b] text-[13px] font-medium text-[#475569] inline-flex items-center justify-center gap-2 transition cursor-pointer">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Mobile Slide-Over Sidebar Drawer --}}
    <div id="adminMobileBackdrop" class="hidden lg:hidden fixed inset-0 z-50 bg-[#0f172a]/45 backdrop-blur-[1px]" data-drawer-close="adminMobileDrawer"></div>
    <aside id="adminMobileDrawer" aria-label="Menu admin seluler" class="hidden lg:hidden fixed inset-y-0 left-0 z-50 w-[280px] max-w-[85vw] bg-white shadow-2xl flex flex-col">
        <div class="h-16 px-4 flex items-center justify-between border-b border-[#0f172a]/8 shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="min-w-0">
                <x-brand-logo size="sm" subtitle="DPM FDIK" />
            </a>
            <button type="button" data-drawer-close="adminMobileDrawer" aria-label="Tutup menu admin" class="w-9 h-9 rounded-[9px] inline-flex items-center justify-center text-[#475569] hover:bg-[#f8fafc] hover:text-[#0f172a] transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3.5 py-4 space-y-5 text-[14.5px]">
            <div>
                <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-[0.08em] text-[#94a3b8]">Operasional</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-[9px] transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#eff6ff] text-[#1e3a8a] font-semibold' : 'text-[#475569] font-medium hover:bg-[#f8fafc]' }}">
                        <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-[9px] transition {{ request()->routeIs('admin.reports.*') ? 'bg-[#eff6ff] text-[#1e3a8a] font-semibold' : 'text-[#475569] font-medium hover:bg-[#f8fafc]' }}">
                        <span class="inline-flex items-center gap-2.5">
                            <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z" /></svg>
                            <span>Laporan Masuk</span>
                        </span>
                        @if ($pendingCount > 0)
                            <span class="font-mono text-[11.5px] font-semibold px-2 py-0.5 rounded-full {{ request()->routeIs('admin.reports.*') ? 'bg-[#1e3a8a] text-white' : 'bg-[#fef3c7] text-[#92400e]' }}">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </div>
            </div>

            <div>
                <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-[0.08em] text-[#94a3b8]">Pengaturan</p>
                <div class="space-y-1">
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-[9px] transition {{ request()->routeIs('admin.categories.*') ? 'bg-[#eff6ff] text-[#1e3a8a] font-semibold' : 'text-[#475569] font-medium hover:bg-[#f8fafc]' }}">
                        <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" /></svg>
                        <span>Kategori Bidang</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 px-3 py-2.5 rounded-[9px] transition {{ request()->routeIs('admin.users.*') ? 'bg-[#eff6ff] text-[#1e3a8a] font-semibold' : 'text-[#475569] font-medium hover:bg-[#f8fafc]' }}">
                        <svg class="w-[18px] h-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" /></svg>
                        <span>Akun Pengurus</span>
                    </a>
                </div>
            </div>

            <div>
                <p class="px-3 mb-2 text-[11px] font-semibold uppercase tracking-[0.08em] text-[#94a3b8]">Portal</p>
                <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between px-3 py-2.5 rounded-[9px] font-medium text-[#475569] hover:bg-[#f8fafc]">
                    <span>Portal Publik</span>
                    <span class="text-[#94a3b8]">&nearr;</span>
                </a>
            </div>
        </nav>

        <div class="p-4 border-t border-[#0f172a]/8 bg-[#f8fafc]/60 shrink-0">
            <div class="flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="text-[13.5px] font-semibold text-[#0f172a] truncate">{{ $user->name }}</p>
                    <p class="text-[12px] text-[#475569] truncate">{{ $user->jabatan ?? $user->email }}</p>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST" class="shrink-0" data-no-loading="1">
                    @csrf
                    <button type="submit" class="px-3 h-9 rounded-[8px] border border-[#dc2626]/30 bg-[#fee2e2]/70 text-[13px] font-medium text-[#991b1b] hover:bg-[#fee2e2] cursor-pointer">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Independent Workspace Shell (offset by fixed sidebar width on lg+) --}}
    <div class="lg:pl-[268px] min-h-screen flex flex-col">
        {{-- Sticky Workspace Topbar --}}
        <header style="view-transition-name: admin-topbar;" class="sticky top-0 z-30 h-16 bg-white/95 backdrop-blur-md border-b border-[#0f172a]/8">
            <div class="h-full px-4 sm:px-8 flex items-center justify-between gap-4">
                {{-- Left: Mobile trigger + Logo OR Desktop Breadcrumb --}}
                <div class="flex items-center gap-3 min-w-0">
                    <button
                        type="button"
                        data-drawer-open="adminMobileDrawer"
                        aria-controls="adminMobileDrawer"
                        aria-expanded="false"
                        aria-label="Buka menu admin"
                        class="lg:hidden inline-flex items-center justify-center w-9 h-9 rounded-[9px] border border-[#0f172a]/15 bg-white text-[#0f172a] hover:bg-[#f8fafc] transition cursor-pointer shrink-0"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    <a href="{{ route('admin.dashboard') }}" class="lg:hidden shrink-0">
                        <x-brand-logo size="sm" />
                    </a>

                    <nav aria-label="Breadcrumb" class="hidden lg:flex items-center gap-2 text-[13.5px] text-[#475569] min-w-0">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-[#0f172a] transition">Panel Admin</a>
                        <span class="text-[#cbd5e1]" aria-hidden="true">/</span>
                        <span class="font-medium text-[#0f172a] truncate">{{ $currentSection }}</span>
                    </nav>
                </div>

                {{-- Right: Quick Pending Indicator & Public Portal Link --}}
                <div class="flex items-center gap-2.5 shrink-0">
                    @if ($pendingCount > 0)
                        <a href="{{ route('admin.reports.index', ['status' => 'menunggu']) }}" class="inline-flex items-center gap-1.5 h-9 px-3 rounded-[9px] bg-[#fffbeb] border border-[#d97706]/25 text-[12.5px] font-medium text-[#92400e] hover:bg-[#fef3c7] transition">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#d97706]" aria-hidden="true"></span>
                            <span class="font-mono font-semibold">{{ $pendingCount }}</span>
                            <span class="hidden sm:inline">menunggu verifikasi</span>
                            <span class="sm:hidden">baru</span>
                        </a>
                    @endif

                    <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="hidden sm:inline-flex items-center gap-1.5 h-9 px-3 rounded-[9px] border border-[#0f172a]/12 bg-white text-[13px] font-medium text-[#475569] hover:text-[#0f172a] hover:bg-[#f8fafc] transition">
                        <span>Portal Publik</span>
                        <span aria-hidden="true">&nearr;</span>
                    </a>
                </div>
            </div>
        </header>

        {{-- Main Workspace Content --}}
        <main class="flex-1 w-full max-w-[1240px] mx-auto px-5 sm:px-8 py-7 sm:py-8">
            @if (session('success'))
                <div class="mb-6 rounded-[10px] bg-[#eff6ff] border border-[#1e3a8a]/25 px-4 py-3 text-[14px] font-medium text-[#1e3a8a]">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-[10px] bg-[#fee2e2] border border-[#dc2626]/25 px-4 py-3 text-[14px] font-medium text-[#991b1b]">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    {{-- Reusable Confirmation Modal --}}
    <div id="sConfirmModal" role="dialog" aria-modal="true" aria-labelledby="sConfirmTitle" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-[#0f172a]/50 backdrop-blur-[2px]">
        <div class="w-full max-w-md rounded-[14px] bg-white border border-[#0f172a]/10 shadow-2xl p-6">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-[10px] bg-[#fee2e2] text-[#b91c1c] flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 id="sConfirmTitle" data-confirm-title class="text-[17px] font-semibold text-[#0f172a]">Konfirmasi penghapusan</h2>
                    <p data-confirm-body class="mt-1.5 text-[14px] text-[#475569] leading-relaxed"></p>
                </div>
            </div>
            <div class="mt-6 flex items-center justify-end gap-2.5">
                <button type="button" data-confirm-cancel class="s-btn-secondary h-[40px] px-4 text-[14px]">Batal</button>
                <button type="button" data-confirm-accept class="inline-flex items-center justify-center h-[40px] px-4 rounded-[10px] bg-[#b91c1c] hover:bg-[#991b1b] text-white text-[14px] font-medium transition cursor-pointer">Ya, Hapus</button>
            </div>
        </div>
    </div>
</body>
</html>
