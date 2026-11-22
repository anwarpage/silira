@extends('layouts.public')

@section('title', 'Tiket ' . $report->ticket_code . ' — Silira')

@section('content')
    <div class="max-w-xl mx-auto px-6 py-14">
        <div class="s-card p-7 sm:p-9">
            <p class="text-[14px] font-medium text-[#456f3a]">Laporan diterima</p>
            <h1 class="mt-1.5 text-[28px] font-medium leading-tight text-[#2a1d17]">Simpan kode tiket Anda</h1>
            <p class="mt-2 text-[15px] text-[#65564c] leading-relaxed">
                Gunakan kode ini untuk memantau progres dan membalas tanggapan resmi dari DPM FDIK kapan saja.
            </p>

            <div class="mt-6 rounded-[10px] border border-[#2a1d17]/16 bg-[#e5e1d8]/65 p-6 text-center">
                <p class="text-[13px] font-medium text-[#65564c]">Kode tiket</p>
                <p id="ticketCodeText" class="mt-1.5 text-[28px] sm:text-[32px] font-medium tracking-wide text-[#2a1d17] font-mono">
                    {{ $report->ticket_code }}
                </p>
                <div class="mt-5 flex items-center justify-center gap-3 no-print">
                    <button type="button" id="copyBtn" class="s-btn-secondary h-[40px] px-4 text-[14px]">
                        Salin kode
                    </button>
                    <button type="button" onclick="window.print()" class="s-btn-secondary h-[40px] px-4 text-[14px]">
                        Cetak bukti
                    </button>
                </div>
            </div>

            <dl class="mt-6 divide-y divide-[#2a1d17]/12 text-[14px]">
                <div class="py-3 flex justify-between gap-4">
                    <dt class="text-[#65564c]">Judul</dt>
                    <dd class="font-medium text-[#2a1d17] text-right">{{ $report->judul_laporan }}</dd>
                </div>
                <div class="py-3 flex justify-between gap-4">
                    <dt class="text-[#65564c]">Klasifikasi &amp; kategori</dt>
                    <dd class="text-[#2a1d17] text-right">{{ $report->jenis_laporan }} &middot; {{ $report->category?->name ?? '-' }}</dd>
                </div>
                <div class="py-3 flex justify-between gap-4">
                    <dt class="text-[#65564c]">Privasi</dt>
                    <dd class="text-[#2a1d17] text-right">{{ $report->privacy_label }}</dd>
                </div>
                <div class="py-3 flex justify-between gap-4">
                    <dt class="text-[#65564c]">Waktu masuk</dt>
                    <dd class="font-mono text-[#2a1d17] text-right">{{ $report->created_at->format('d M Y, H:i') }} WIB</dd>
                </div>
            </dl>

            <div class="mt-8 flex flex-col sm:flex-row gap-3 no-print">
                <a href="{{ route('tracking.show', $report->ticket_code) }}" class="s-btn-primary flex-1">
                    Pantau tiket ini
                </a>
                <a href="{{ route('home') }}" class="s-btn-secondary">
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
                setTimeout(() => { btn.textContent = 'Salin kode'; }, 1800);
            } catch {
                alert('Kode tiket: ' + code);
            }
        });
    </script>
@endsection
