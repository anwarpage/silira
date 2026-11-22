<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Masuk admin — Silira</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Onest:wght@400..600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-[#e5e1d8] text-[#2a1d17]">
    <div class="w-full max-w-[420px]">
        <div class="mb-7">
            <a href="{{ route('home') }}">
                <x-brand-logo size="lg" />
            </a>
        </div>

        <div class="s-card p-7 sm:p-8">
            <h1 class="text-[24px] font-medium text-[#2a1d17]">Masuk portal admin</h1>
            <p class="mt-1.5 text-[14px] text-[#65564c]">Khusus pengurus DPM Fakultas Dakwah dan Ilmu Komunikasi.</p>

            @if (session('status'))
                <div class="mt-5 rounded-[8px] bg-[#d9e7d6] border border-[#456f3a]/30 p-3.5 text-[14px] text-[#284b20]">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-5 rounded-[8px] bg-[#eedcdb] border border-[#9b3a34]/30 p-3.5 text-[14px] text-[#7a2824]">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-[13px] font-medium text-[#65564c] mb-1.5">Email pengurus</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="admin@silira.ac.id" class="s-input">
                </div>

                <div>
                    <label for="password" class="block text-[13px] font-medium text-[#65564c] mb-1.5">Kata sandi</label>
                    <input type="password" name="password" id="password" required placeholder="Masukkan kata sandi" class="s-input">
                </div>

                <label class="inline-flex items-center gap-2 text-[14px] text-[#65564c] cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-[#2a1d17]/30 accent-[#456f3a]">
                    Ingat sesi saya
                </label>

                <button type="submit" class="s-btn-primary w-full h-[48px]">
                    Masuk dashboard
                </button>
            </form>
        </div>

        <p class="mt-5 text-[14px] text-[#65564c]">
            <a href="{{ route('home') }}" class="hover:text-[#2a1d17] transition">&larr; Kembali ke beranda</a>
        </p>
    </div>
</body>
</html>
