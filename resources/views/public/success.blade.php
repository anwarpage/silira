@extends('layouts.public')

@section('title', 'Tiket ' . $report->ticket_code . ' — SILIRA')

@section('content')
    <div class="max-w-xl mx-auto px-4 sm:px-6 py-12">
        <div class="bg-white rounded-xl border border-stone-200 p-6 sm:p-8">
            <p class="text-xs font-semibold text-emerald-800">Laporan Diterima</p>
            <h1 class="mt-1 text-xl font-bold tracking-tight text-stone-950">Simpan Kode Tiket Anda</h1>
            <p class="mt-1.5 text-sm text-stone-600">Gunakan kode ini untuk memantau status dan membaca tanggapan resmi DPM FDIK kapan saja.</p>

            <div class="mt-6 rounded-lg border border-stone-200 bg-stone-50 p-5 text-center">
                <p class="text-[11px] font-medium uppercase tracking-wider text-stone-500">Kode Tiket</p>
                <p id="ticketCodeText" class="mt-1.5 text-2xl sm:text-3xl font-bold tracking-wider text-stone-950 font-mono">
                    {{ $report->ticket_code }}
                </p>
                <div class="mt-4 flex items-center justify-center gap-2 no-print">
                    <button type="button" id="copyBtn" class="btn-press px-3.5 py-1.5 rounded-lg bg-stone-900 text-white text-xs font-medium hover:bg-stone-800 cursor-pointer">
                        Salin Kode
                    </button>
                    <button type="button" onclick="window.print()" class="btn-press px-3.5 py-1.5 rounded-lg border border-stone-300 bg-white text-stone-700 text-xs font-medium hover:bg-stone-50 cursor-pointer">
                        Cetak Bukti
                    </button>
                </div>
            </div>

            <dl class="mt-6 divide-y divide-stone-100 text-xs">
                <div class="py-2.5 flex justify-between gap-4">
                    <dt class="text-stone-500">Judul</dt>
                    <dd class="font-medium text-stone-900 text-right">{{ $report->judul_laporan }}</dd>
                </div>
                <div class="py-2.5 flex justify-between gap-4">
                    <dt class="text-stone-500">Klasifikasi / Kategori</dt>
                    <dd class="text-stone-800 text-right">{{ $report->jenis_laporan }} &middot; {{ $report->category?->name ?? '-' }}</dd>
                </div>
                <div class="py-2.5 flex justify-between gap-4">
                    <dt class="text-stone-500">Privasi</dt>
                    <dd class="text-stone-800 text-right">{{ $report->privacy_label }}</dd>
                </div>
                <div class="py-2.5 flex justify-between gap-4">
                    <dt class="text-stone-500">Waktu Masuk</dt>
                    <dd class="font-mono text-stone-800 text-right">{{ $report->created_at->format('d M Y, H:i') }} WIB</dd>
                </div>
            </dl>

            <div class="mt-7 flex flex-col sm:flex-row gap-2.5 no-print">
                <a href="{{ route('tracking.show', $report->ticket_code) }}" class="btn-press flex-1 text-center px-4 py-2.5 rounded-lg bg-emerald-900 text-white text-sm font-semibold hover:bg-emerald-950">
                    Pantau Tiket Ini
                </a>
                <a href="{{ route('home') }}" class="btn-press px-4 py-2.5 rounded-lg border border-stone-300 bg-white text-stone-700 text-sm font-medium hover:bg-stone-50 text-center">
                    Beranda
                </a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('copyBtn')?.addEventListener('click', async () => {
            const code = document.getElementById('ticketCodeText').textContent.trim();
            const btn = document.getElementById('copyBtn');
            try {
                await navigator.clipboard.writeText(code);
                btn.textContent = 'Tersalin';
                setTimeout(() => { btn.textContent = 'Salin Kode'; }, 1800);
            } catch {
                alert('Kode tiket: ' + code);
            }
        });
    </script>
@endsection
