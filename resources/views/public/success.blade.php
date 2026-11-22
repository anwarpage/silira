@extends('layouts.public')

@section('title', 'Laporan Terkirim — Kode Tiket ' . $report->ticket_code)

@section('content')
    <div class="max-w-2xl mx-auto px-4 sm:px-6 py-12">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-10 text-center">
            <div class="mx-auto w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
            </div>

            <h1 class="mt-4 text-2xl font-extrabold text-slate-900">Laporan Anda Berhasil Dikirim!</h1>
            <p class="mt-2 text-sm text-slate-600">
                Simpan <strong>Kode Tiket</strong> di bawah ini untuk melacak status penanganan dan membaca balasan resmi dari DPM FDIK kapan saja.
            </p>

            {{-- Ticket Code Box --}}
            <div class="mt-6 bg-slate-50 border-2 border-dashed border-slate-300 rounded-2xl p-6">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Kode Tiket Laporan Anda</p>
                <p id="ticketCodeText" class="mt-2 text-3xl sm:text-4xl font-extrabold tracking-widest text-red-700 font-mono">
                    {{ $report->ticket_code }}
                </p>
                <div class="mt-4 flex flex-wrap items-center justify-center gap-2 no-print">
                    <button type="button" id="copyBtn" class="px-4 py-2 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition cursor-pointer">
                        Salin Kode Tiket
                    </button>
                    <button type="button" onclick="window.print()" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-100 transition cursor-pointer">
                        Cetak Bukti Lapor
                    </button>
                </div>
            </div>

            {{-- Summary --}}
            <dl class="mt-6 text-left bg-slate-50/70 rounded-xl p-5 space-y-2.5 text-sm border border-slate-100">
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Judul</dt>
                    <dd class="font-semibold text-slate-900 text-right">{{ $report->judul_laporan }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Klasifikasi / Kategori</dt>
                    <dd class="font-medium text-slate-800 text-right">{{ $report->jenis_laporan }} &middot; {{ $report->category?->name ?? '-' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Privasi</dt>
                    <dd class="font-medium text-slate-800 text-right">{{ $report->privacy_label }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-500">Waktu Masuk</dt>
                    <dd class="font-medium text-slate-800 text-right">{{ $report->created_at->format('d M Y, H:i') }} WIB</dd>
                </div>
            </dl>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3 no-print">
                <a href="{{ route('tracking.show', $report->ticket_code) }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-red-700 text-white font-bold text-sm hover:bg-red-800 transition">
                    Pantau Status Tiket Ini &rarr;
                </a>
                <a href="{{ route('home') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl border border-slate-300 text-slate-700 font-semibold text-sm hover:bg-slate-100 transition">
                    Kembali ke Beranda
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
                btn.textContent = 'Kode Tersalin!';
                setTimeout(() => { btn.textContent = 'Salin Kode Tiket'; }, 2000);
            } catch {
                alert('Kode tiket: ' + code);
            }
        });
    </script>
@endsection
