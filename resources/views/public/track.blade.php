@extends('layouts.public')

@section('title', $report ? "Tiket {$report->ticket_code} — SILIRA" : 'Lacak Tiket — SILIRA')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-10">
        <div class="bg-white rounded-xl border border-stone-200 p-5 sm:p-6">
            <h1 class="text-lg font-bold tracking-tight text-stone-950">Lacak Status Laporan</h1>
            <p class="mt-1 text-xs text-stone-600">Masukkan kode tiket untuk melihat progres dan berinteraksi dengan Admin DPM.</p>

            <form action="{{ route('tracking.lookup') }}" method="POST" class="mt-4 flex flex-col sm:flex-row gap-2.5">
                @csrf
                <input
                    type="text"
                    name="ticket_code"
                    value="{{ old('ticket_code', $searchedCode) }}"
                    placeholder="Contoh: SLR-2026-FDIK01"
                    class="flex-1 px-3.5 py-2.5 rounded-lg border border-stone-300 bg-stone-50/70 text-sm font-mono uppercase placeholder:normal-case placeholder:font-sans focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-900/15 focus:border-emerald-800"
                    required
                >
                <button type="submit" class="btn-press px-5 py-2.5 rounded-lg bg-emerald-900 text-white text-sm font-semibold hover:bg-emerald-950 cursor-pointer">
                    Cari Tiket
                </button>
            </form>
        </div>

        @if (session('success'))
            <div class="mt-5 rounded-lg bg-emerald-50 border border-emerald-200 px-4 py-3 text-xs font-medium text-emerald-900">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mt-5 rounded-lg bg-rose-50 border border-rose-200 px-4 py-3 text-xs font-medium text-rose-900">
                {{ session('error') }}
            </div>
        @endif

        @if ($searchedCode && ! $report)
            <div class="mt-5 bg-white rounded-xl border border-stone-200 p-8 text-center">
                <p class="text-sm font-semibold text-stone-900">Kode tiket <span class="font-mono text-rose-700">{{ $searchedCode }}</span> tidak ditemukan.</p>
                <p class="mt-1 text-xs text-stone-500">Pastikan kode tiket sesuai dengan yang Anda terima saat melapor.</p>
            </div>
        @endif

        @if ($report)
            @php
                $steps = ['menunggu' => 'Menunggu', 'diverifikasi' => 'Diverifikasi', 'diproses' => 'Diproses', 'selesai' => 'Selesai'];
                $order = ['menunggu' => 1, 'diverifikasi' => 2, 'diproses' => 3, 'selesai' => 4, 'ditolak' => 0];
                $currentStep = $order[$report->status] ?? 1;
            @endphp

            <div class="mt-5 bg-white rounded-xl border border-stone-200 p-6">
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="font-mono font-semibold px-2 py-0.5 rounded bg-stone-900 text-white">{{ $report->ticket_code }}</span>
                    <span class="px-2 py-0.5 rounded-md font-medium {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
                    <span class="text-stone-500">{{ $report->jenis_laporan }}</span>
                    <span class="text-stone-300">&middot;</span>
                    <span class="text-stone-500">{{ $report->privacy_label }}</span>
                </div>

                <h2 class="mt-3 text-xl font-bold tracking-tight text-stone-950">{{ $report->judul_laporan }}</h2>
                <p class="mt-1 text-xs text-stone-500">
                    {{ $report->public_reporter_name }}
                    @if ($report->category) &middot; {{ $report->category->name }} @endif
                    &middot; {{ $report->created_at->format('d M Y, H:i') }} WIB
                </p>

                @if ($report->status === 'ditolak')
                    <div class="mt-5 rounded-lg bg-rose-50 border border-rose-200 p-3.5 text-xs text-rose-900">
                        <strong>Ditolak:</strong> Lihat alasan resmi dari Admin DPM pada riwayat tindak lanjut di bawah.
                    </div>
                @else
                    <div class="mt-5 grid grid-cols-4 gap-2">
                        @foreach ($steps as $key => $label)
                            @php $done = $currentStep >= $order[$key]; @endphp
                            <div>
                                <div class="h-1.5 rounded-full {{ $done ? 'bg-emerald-800' : 'bg-stone-200' }}"></div>
                                <span class="block mt-1.5 text-[11px] {{ $done ? 'font-semibold text-stone-900' : 'text-stone-400' }}">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-5 pt-5 border-t border-stone-100">
                    <p class="text-sm text-stone-800 leading-relaxed whitespace-pre-line">{{ $report->isi_laporan }}</p>

                    @if ($report->tanggal_kejadian || $report->lokasi_kejadian)
                        <div class="mt-4 flex flex-wrap gap-4 text-xs text-stone-600 bg-stone-50 rounded-lg px-3.5 py-2.5 border border-stone-100">
                            @if ($report->tanggal_kejadian)
                                <span><strong class="text-stone-800">Tanggal:</strong> {{ $report->tanggal_kejadian->format('d M Y') }}</span>
                            @endif
                            @if ($report->lokasi_kejadian)
                                <span><strong class="text-stone-800">Lokasi:</strong> {{ $report->lokasi_kejadian }}</span>
                            @endif
                        </div>
                    @endif

                    @if ($report->attachment_path)
                        <div class="mt-4">
                            @if ($report->isAttachmentImage())
                                <a href="{{ asset('storage/' . $report->attachment_path) }}" target="_blank" class="inline-block">
                                    <img src="{{ asset('storage/' . $report->attachment_path) }}" alt="Lampiran Bukti" class="max-h-60 rounded-lg border border-stone-200">
                                </a>
                            @else
                                <a href="{{ asset('storage/' . $report->attachment_path) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-stone-100 text-stone-800 text-xs font-medium hover:bg-stone-200">
                                    Unduh Lampiran ({{ $report->attachment_name ?? 'Berkas' }})
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Timeline --}}
            <div class="mt-5 bg-white rounded-xl border border-stone-200 p-6">
                <h3 class="text-sm font-semibold text-stone-950">Riwayat Tindak Lanjut</h3>

                <div class="mt-4 space-y-3">
                    @forelse ($report->publicResponses as $res)
                        <div class="rounded-lg border p-4 {{ $res->sender_type === 'admin' ? 'border-emerald-200/80 bg-emerald-50/30' : 'border-stone-200 bg-stone-50/50' }}">
                            <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-stone-900">{{ $res->sender_name }}</span>
                                    <span class="text-[11px] px-1.5 py-0.5 rounded bg-white border border-stone-200 text-stone-600">
                                        {{ $res->sender_type === 'admin' ? 'Admin DPM' : ($res->sender_type === 'reporter' ? 'Pelapor' : 'Sistem') }}
                                    </span>
                                    @if ($res->status_to)
                                        <span class="text-[11px] font-medium text-emerald-900">&rarr; {{ \App\Models\Report::STATUSES[$res->status_to] ?? $res->status_to }}</span>
                                    @endif
                                </div>
                                <time class="font-mono text-[11px] text-stone-500">{{ $res->created_at->format('d M Y, H:i') }}</time>
                            </div>
                            <p class="mt-2 text-sm text-stone-800 leading-relaxed whitespace-pre-line">{{ $res->message }}</p>
                            @if ($res->attachment_path)
                                <a href="{{ asset('storage/' . $res->attachment_path) }}" target="_blank" class="mt-2 inline-block text-xs font-medium text-emerald-900 hover:underline">
                                    Lampiran: {{ $res->attachment_name ?? 'Berkas' }}
                                </a>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-stone-500">Belum ada riwayat tindak lanjut.</p>
                    @endforelse
                </div>

                @if ($report->canReporterReply())
                    <form action="{{ route('tracking.reply', $report->ticket_code) }}" method="POST" enctype="multipart/form-data" class="mt-6 pt-5 border-t border-stone-200 space-y-3">
                        @csrf
                        <label class="block text-xs font-semibold text-stone-800">Tambah Tanggapan atau Bukti</label>
                        <textarea name="message" rows="3" required placeholder="Tulis klarifikasi atau informasi tambahan..." class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/15 focus:border-emerald-800">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf" class="text-xs text-stone-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border file:border-stone-200 file:text-xs file:font-medium file:bg-white file:text-stone-700 hover:file:bg-stone-50 cursor-pointer">
                            <button type="submit" class="btn-press px-4 py-2 rounded-lg bg-stone-900 text-white text-xs font-semibold hover:bg-stone-800 cursor-pointer">
                                Kirim Tanggapan
                            </button>
                        </div>
                    </form>
                @else
                    <p class="mt-5 pt-4 border-t border-stone-100 text-xs text-stone-500">
                        Tiket ini berstatus <strong>{{ $report->status_label }}</strong> dan ditutup dari tanggapan baru.
                    </p>
                @endif
            </div>
        @endif
    </div>
@endsection
