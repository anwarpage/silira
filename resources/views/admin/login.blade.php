<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#f8fafc">
    <title>Masuk Admin — Silira</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400..700;1,9..40,400..700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-[#f8fafc] text-[#0f172a]">
    <div class="w-full max-w-[400px]">
        <div class="mb-7 flex justify-center">
            <a href="{{ route('home') }}">
                <x-brand-logo size="lg" />
            </a>
        </div>

        <div class="s-card-accent p-7 sm:p-8">
            <h1 class="text-[22px] font-semibold text-[#0f172a]">Masuk Portal Admin</h1>
            <p class="mt-1 text-[14px] text-[#475569]">Khusus pengurus DPM FDIK.</p>

            @if (session('status'))
                <div class="mt-5 rounded-[9px] bg-[#eff6ff] border border-[#1e3a8a]/25 p-3.5 text-[14px] text-[#1e3a8a]">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-5 rounded-[9px] bg-[#fee2e2] border border-[#dc2626]/25 p-3.5 text-[14px] text-[#991b1b]">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Email pengurus</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="admin@silira.ac.id" class="s-input">
                </div>

                <div>
                    <label for="password" class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Kata sandi</label>
                    <input type="password" name="password" id="password" required placeholder="Masukkan kata sandi" class="s-input">
                </div>

                <label class="inline-flex items-center gap-2 text-[14px] text-[#475569] cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-[#0f172a]/25 accent-[#1e3a8a]">
                    Ingat sesi saya
                </label>

                <button type="submit" data-loading-text="Memverifikasi..." class="s-btn-primary w-full h-[44px]">
                    Masuk Dashboard
                </button>
            </form>
        </div>

        <p class="mt-5 text-center text-[13.5px] text-[#475569]">
            <a href="{{ route('home') }}" class="hover:text-[#0f172a] transition">&larr; Kembali ke beranda</a>
        </p>
    </div>
</body>
</html>
