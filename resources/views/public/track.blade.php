@extends('layouts.public')

@section('title', $report ? "Tiket {$report->ticket_code} — Lacak SILIRA" : 'Lacak Status Laporan — SILIRA')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-10">
        {{-- Search Bar --}}
        <div class="bg-white rounded-2xl border border-slate-200/90 p-6 sm:p-7 shadow-2xs">
            <span class="text-xs font-bold uppercase tracking-wider text-red-700">Pelacakan Tanpa Login</span>
            <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950">Lacak Status Tiket Laporan</h1>
            <p class="mt-1 text-sm text-slate-600">Masukkan kode tiket yang Anda terima saat mengirim aspirasi untuk melihat progres dan membalas tanggapan DPM.</p>

            <form action="{{ route('tracking.lookup') }}" method="POST" class="mt-5 flex flex-col sm:flex-row gap-3">
                @csrf
                <input
                    type="text"
                    name="ticket_code"
                    value="{{ old('ticket_code', $searchedCode) }}"
                    placeholder="Contoh: SLR-2026-FDIK01"
                    class="flex-1 px-4 py-3 rounded-xl border border-slate-300/90 bg-slate-50/70 text-sm font-mono uppercase placeholder:normal-case placeholder:font-sans focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600 transition"
                    required
                >
                <button type="submit" class="btn-press px-6 py-3 rounded-xl bg-red-700 text-white font-bold text-sm hover:bg-red-800 cursor-pointer">
                    Cari Tiket
                </button>
            </form>
        </div>

        @if (session('success'))
            <div class="mt-6 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mt-6 rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm font-medium text-rose-800">
                {{ session('error') }}
            </div>
        @endif

        @if ($searchedCode && ! $report)
            <div class="mt-6 bg-white rounded-2xl border border-rose-200 p-8 text-center">
                <p class="text-base font-bold text-slate-900">Kode tiket <span class="font-mono text-red-700">{{ $searchedCode }}</span> tidak ditemukan.</p>
                <p class="mt-1.5 text-sm text-slate-600">Pastikan penulisan kode tiket sudah benar termasuk tanda hubung, misal <span class="font-mono">SLR-2026-FDIK01</span>.</p>
            </div>
        @endif

        @if ($report)
            @php
                $steps = [
                    'menunggu' => 'Menunggu',
                    'diverifikasi' => 'Diverifikasi',
                    'diproses' => 'Diproses',
                    'selesai' => 'Selesai',
                ];
                $order = ['menunggu' => 1, 'diverifikasi' => 2, 'diproses' => 3, 'selesai' => 4, 'ditolak' => 0];
                $currentStep = $order[$report->status] ?? 1;
            @endphp

            <div class="mt-6 bg-white rounded-2xl border border-slate-200/90 p-6 sm:p-8 shadow-2xs">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-mono text-xs font-bold px-2.5 py-1 rounded-lg bg-slate-900 text-white">{{ $report->ticket_code }}</span>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">{{ $report->jenis_laporan }}</span>
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">{{ $report->privacy_label }}</span>
                </div>

                <h2 class="mt-4 text-xl sm:text-2xl font-extrabold tracking-tight text-slate-950">{{ $report->judul_laporan }}</h2>
                <p class="mt-1.5 text-xs text-slate-500">
                    Dikirim oleh <strong class="text-slate-800">{{ $report->public_reporter_name }}</strong>
                    @if ($report->category) &middot; Kategori: <strong class="text-slate-800">{{ $report->category->name }}</strong> @endif
                    &middot; {{ $report->created_at->format('d M Y, H:i') }} WIB
                </p>

                @if ($report->status === 'ditolak')
                    <div class="mt-6 rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800">
                        <strong>Status Ditolak:</strong> Laporan ini tidak dapat dilanjutkan. Lihat penjelasan resmi dari Admin DPM pada riwayat tindak lanjut di bawah.
                    </div>
                @else
                    <div class="mt-6 grid grid-cols-4 gap-2.5">
                        @foreach ($steps as $key => $label)
                            @php $done = $currentStep >= $order[$key]; @endphp
                            <div class="flex flex-col items-center text-center">
                                <div class="w-full h-2 rounded-full {{ $done ? 'bg-red-700' : 'bg-slate-200' }}"></div>
                                <span class="mt-2 text-xs {{ $done ? 'font-bold text-slate-900' : 'font-medium text-slate-400' }}">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-6 pt-6 border-t border-slate-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Uraian Laporan</h3>
                    <p class="mt-2.5 text-sm text-slate-800 leading-relaxed whitespace-pre-line">{{ $report->isi_laporan }}</p>

                    @if ($report->tanggal_kejadian || $report->lokasi_kejadian)
                        <div class="mt-4 flex flex-wrap gap-4 text-xs text-slate-600 bg-slate-50 rounded-xl p-3.5 border border-slate-100">
                            @if ($report->tanggal_kejadian)
                                <span><strong>Tanggal Kejadian:</strong> {{ $report->tanggal_kejadian->format('d M Y') }}</span>
                            @endif
                            @if ($report->lokasi_kejadian)
                                <span><strong>Lokasi:</strong> {{ $report->lokasi_kejadian }}</span>
                            @endif
                        </div>
                    @endif

                    @if ($report->attachment_path)
                        <div class="mt-5">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Lampiran Bukti</p>
                            @if ($report->isAttachmentImage())
                                <a href="{{ asset('storage/' . $report->attachment_path) }}" target="_blank" class="inline-block">
                                    <img src="{{ asset('storage/' . $report->attachment_path) }}" alt="Lampiran Bukti" class="max-h-64 rounded-xl border border-slate-200">
                                </a>
                            @else
                                <a href="{{ asset('storage/' . $report->attachment_path) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 text-slate-800 text-xs font-semibold hover:bg-slate-200 transition">
                                    Unduh Lampiran ({{ $report->attachment_name ?? 'Berkas' }})
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Timeline --}}
            <div class="mt-6 bg-white rounded-2xl border border-slate-200/90 p-6 sm:p-8 shadow-2xs">
                <h3 class="text-lg font-extrabold text-slate-950">Riwayat Progres &amp; Tanggapan</h3>

                <ol class="mt-6 relative border-s border-slate-200 ml-3 space-y-5">
                    @forelse ($report->publicResponses as $res)
                        <li class="ms-6">
                            <span class="absolute -start-2.5 mt-1.5 flex h-5 w-5 items-center justify-center rounded-full ring-4 ring-white {{ $res->sender_type === 'admin' ? 'bg-red-700' : ($res->sender_type === 'reporter' ? 'bg-slate-900' : 'bg-slate-400') }}"></span>

                            <div class="rounded-xl border {{ $res->sender_type === 'admin' ? 'border-red-100 bg-red-50/40' : 'border-slate-200 bg-slate-50/60' }} p-4">
                                <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-bold text-slate-900">{{ $res->sender_name }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $res->sender_type === 'admin' ? 'bg-red-100 text-red-800' : ($res->sender_type === 'reporter' ? 'bg-slate-200 text-slate-800' : 'bg-slate-100 text-slate-600') }}">
                                            {{ $res->sender_type === 'admin' ? 'Admin DPM' : ($res->sender_type === 'reporter' ? 'Pelapor' : 'Sistem') }}
                                        </span>
                                        @if ($res->status_to)
                                            <span class="text-[11px] font-semibold text-slate-600">
                                                &rarr; {{ \App\Models\Report::STATUSES[$res->status_to] ?? $res->status_to }}
                                            </span>
                                        @endif
                                    </div>
                                    <time class="text-slate-500">{{ $res->created_at->format('d M Y, H:i') }}</time>
                                </div>
                                <p class="mt-2 text-sm text-slate-800 leading-relaxed whitespace-pre-line">{{ $res->message }}</p>

                                @if ($res->attachment_path)
                                    <a href="{{ asset('storage/' . $res->attachment_path) }}" target="_blank" class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold text-red-700 hover:underline">
                                        Lihat Lampiran ({{ $res->attachment_name ?? 'Berkas' }})
                                    </a>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="ms-6 text-sm text-slate-500">Belum ada riwayat tindak lanjut.</li>
                    @endforelse
                </ol>

                @if ($report->canReporterReply())
                    <div class="mt-8 pt-6 border-t border-slate-200">
                        <h4 class="text-sm font-bold text-slate-950">Kirim Tanggapan / Klarifikasi Tambahan</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Sampaikan informasi tambahan atau bukti baru langsung ke Admin DPM melalui tiket ini.</p>

                        <form action="{{ route('tracking.reply', $report->ticket_code) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-3">
                            @csrf
                            <textarea name="message" rows="3" required placeholder="Tulis tanggapan Anda..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300/90 text-sm focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-xs text-rose-600">{{ $message }}</p>
                            @enderror

                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf" class="text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                                <button type="submit" class="btn-press px-5 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 cursor-pointer">
                                    Kirim Tanggapan
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="mt-6 pt-4 border-t border-slate-100 text-xs text-slate-500">
                        Tiket ini berstatus <strong>{{ $report->status_label }}</strong> dan ditutup dari tanggapan baru.
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection
