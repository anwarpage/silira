<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — SILIRA</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F7F7F5] text-stone-900 antialiased min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="mb-6">
            <a href="{{ route('home') }}">
                <x-brand-logo subtitle="Portal Admin DPM FDIK" />
            </a>
        </div>

        <div class="bg-white rounded-xl border border-stone-200 p-6">
            <h1 class="text-base font-bold tracking-tight text-stone-950">Masuk ke Dashboard</h1>
            <p class="mt-1 text-xs text-stone-500">Gunakan akun pengurus DPM FDIK.</p>

            @if (session('status'))
                <div class="mt-4 rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-xs text-emerald-900">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-4 rounded-lg bg-rose-50 border border-rose-200 p-3 text-xs text-rose-900">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-medium text-stone-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="nama@silira.ac.id" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/15 focus:border-emerald-800">
                </div>

                <div>
                    <label for="password" class="block text-xs font-medium text-stone-700 mb-1">Kata Sandi</label>
                    <input type="password" name="password" id="password" required placeholder="••••••••" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/15 focus:border-emerald-800">
                </div>

                <label class="inline-flex items-center gap-2 text-xs text-stone-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded border-stone-300 text-emerald-900 focus:ring-emerald-800">
                    Ingat sesi saya
                </label>

                <button type="submit" class="btn-press w-full py-2.5 rounded-lg bg-stone-900 text-white text-sm font-semibold hover:bg-stone-800 cursor-pointer">
                    Masuk
                </button>
            </form>
        </div>

        <p class="mt-4 text-center">
            <a href="{{ route('home') }}" class="text-xs text-stone-500 hover:text-stone-900">&larr; Kembali ke Beranda</a>
        </p>
    </div>
</body>
</html>
