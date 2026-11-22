@extends('layouts.public')

@section('title', $report ? "Tiket {$report->ticket_code} — Silira" : 'Lacak tiket — Silira')

@section('content')
    <div class="max-w-3xl mx-auto px-6 py-12">
        <div class="s-card p-6 sm:p-7">
            <h1 class="text-[26px] font-medium text-[#0f172a]">Lacak status tiket</h1>
            <p class="mt-1.5 text-[15px] text-[#475569]">Masukkan kode tiket untuk melihat progres dan mengirim tanggapan tambahan.</p>

            <form action="{{ route('tracking.lookup') }}" method="POST" class="mt-5 flex flex-col sm:flex-row gap-3">
                @csrf
                <input
                    type="text"
                    name="ticket_code"
                    value="{{ old('ticket_code', $searchedCode) }}"
                    placeholder="Contoh: SLR-2026-FDIK01"
                    class="s-input font-mono uppercase placeholder:normal-case placeholder:font-sans"
                    required
                >
                <button type="submit" class="s-btn-primary h-[48px] shrink-0">
                    Cari tiket
                </button>
            </form>
        </div>

        @if (session('success'))
            <div class="mt-5 rounded-[10px] bg-[#eff4ff] border border-[#1e3a8a]/30 px-4 py-3 text-[14px] font-medium text-[#1e3a8a]">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mt-5 rounded-[10px] bg-[#fee2e2] border border-[#dc2626]/30 px-4 py-3 text-[14px] font-medium text-[#991b1b]">
                {{ session('error') }}
            </div>
        @endif

        @if ($searchedCode && ! $report)
            <div class="mt-6 s-card p-8 text-center">
                <p class="text-[16px] font-medium text-[#0f172a]">Kode tiket <span class="font-mono text-[#991b1b]">{{ $searchedCode }}</span> tidak ditemukan.</p>
                <p class="mt-1.5 text-[14px] text-[#475569]">Pastikan penulisan kode tiket sesuai dengan yang Anda terima saat melapor.</p>
            </div>
        @endif

        @if ($report)
            @php
                $steps = ['menunggu' => 'Menunggu', 'diverifikasi' => 'Diverifikasi', 'diproses' => 'Diproses', 'selesai' => 'Selesai'];
                $order = ['menunggu' => 1, 'diverifikasi' => 2, 'diproses' => 3, 'selesai' => 4, 'ditolak' => 0];
                $currentStep = $order[$report->status] ?? 1;
            @endphp

            <div class="mt-6 s-card p-6 sm:p-8">
                <div class="flex flex-wrap items-center gap-2.5 text-[13px]">
                    <span class="font-mono font-medium px-2.5 py-0.5 rounded-[6px] bg-[#0f172a] text-[#f5f7fa]">{{ $report->ticket_code }}</span>
                    <span class="px-2.5 py-0.5 rounded-[6px] font-medium {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
                    <span class="text-[#475569]">{{ $report->jenis_laporan }}</span>
                    <span class="text-[#475569]">&middot; {{ $report->privacy_label }}</span>
                </div>

                <h2 class="mt-4 text-[24px] font-medium leading-snug text-[#0f172a]">{{ $report->judul_laporan }}</h2>
                <p class="mt-1.5 text-[14px] text-[#475569]">
                    {{ $report->public_reporter_name }}
                    @if ($report->category) &middot; {{ $report->category->name }} @endif
                    &middot; {{ $report->created_at->format('d M Y, H:i') }} WIB
                </p>

                @if ($report->status === 'ditolak')
                    <div class="mt-6 rounded-[8px] bg-[#fee2e2] border border-[#dc2626]/25 p-4 text-[14px] text-[#991b1b]">
                        Laporan ini berstatus Ditolak. Lihat alasan resmi dari Admin DPM pada riwayat tindak lanjut di bawah.
                    </div>
                @else
                    <div class="mt-6 grid grid-cols-4 gap-2.5">
                        @foreach ($steps as $key => $label)
                            @php $done = $currentStep >= $order[$key]; @endphp
                            <div>
                                <div class="h-1.5 rounded-full {{ $done ? 'bg-[#1e3a8a]' : 'bg-[#eceff4]' }}"></div>
                                <span class="block mt-2 text-[13px] {{ $done ? 'font-medium text-[#0f172a]' : 'text-[#475569]' }}">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-6 pt-6 border-t border-[#0f172a]/12">
                    <p class="text-[16px] text-[#0f172a] leading-relaxed whitespace-pre-line">{{ $report->isi_laporan }}</p>

                    @if ($report->tanggal_kejadian || $report->lokasi_kejadian)
                        <div class="mt-5 flex flex-wrap gap-5 text-[14px] text-[#475569] bg-[#f5f7fa]/65 rounded-[8px] px-4 py-3">
                            @if ($report->tanggal_kejadian)
                                <span><strong class="text-[#0f172a] font-medium">Tanggal:</strong> {{ $report->tanggal_kejadian->format('d M Y') }}</span>
                            @endif
                            @if ($report->lokasi_kejadian)
                                <span><strong class="text-[#0f172a] font-medium">Lokasi:</strong> {{ $report->lokasi_kejadian }}</span>
                            @endif
                        </div>
                    @endif

                    @if ($report->attachment_path)
                        <div class="mt-5">
                            @if ($report->isAttachmentImage())
                                <a href="{{ asset('storage/' . $report->attachment_path) }}" target="_blank" class="inline-block">
                                    <img src="{{ asset('storage/' . $report->attachment_path) }}" alt="Lampiran Bukti" class="max-h-64 rounded-[8px] border border-[#0f172a]/15">
                                </a>
                            @else
                                <a href="{{ asset('storage/' . $report->attachment_path) }}" target="_blank" class="s-btn-secondary h-[40px] text-[14px]">
                                    Unduh lampiran ({{ $report->attachment_name ?? 'Berkas' }})
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Timeline --}}
            <div class="mt-6 s-card p-6 sm:p-8">
                <h3 class="text-[18px] font-medium text-[#0f172a]">Riwayat tindak lanjut</h3>

                <div class="mt-5 divide-y divide-[#0f172a]/12">
                    @forelse ($report->publicResponses as $res)
                        <div class="py-4 first:pt-0">
                            <div class="flex flex-wrap items-center justify-between gap-2 text-[13px]">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-medium text-[#0f172a]">{{ $res->sender_name }}</span>
                                    <span class="px-2 py-0.5 rounded-[6px] bg-[#f5f7fa] text-[#475569]">
                                        {{ $res->sender_type === 'admin' ? 'Admin DPM' : ($res->sender_type === 'reporter' ? 'Pelapor' : 'Sistem') }}
                                    </span>
                                    @if ($res->status_to)
                                        <span class="font-medium text-[#1e3a8a]">&rarr; {{ \App\Models\Report::STATUSES[$res->status_to] ?? $res->status_to }}</span>
                                    @endif
                                </div>
                                <time class="font-mono text-[#475569]">{{ $res->created_at->format('d M Y, H:i') }}</time>
                            </div>
                            <p class="mt-2 text-[15px] text-[#0f172a] leading-relaxed whitespace-pre-line">{{ $res->message }}</p>
                            @if ($res->attachment_path)
                                <a href="{{ asset('storage/' . $res->attachment_path) }}" target="_blank" class="mt-2 inline-block text-[14px] font-medium text-[#1e3a8a] hover:underline">
                                    Lampiran: {{ $res->attachment_name ?? 'Berkas' }}
                                </a>
                            @endif
                        </div>
                    @empty
                        <p class="text-[15px] text-[#475569]">Belum ada riwayat tindak lanjut.</p>
                    @endforelse
                </div>

                @if ($report->canReporterReply())
                    <form action="{{ route('tracking.reply', $report->ticket_code) }}" method="POST" enctype="multipart/form-data" class="mt-6 pt-6 border-t border-[#0f172a]/15 space-y-3.5">
                        @csrf
                        <label class="block text-[14px] font-medium text-[#0f172a]">Tambah tanggapan atau bukti</label>
                        <textarea name="message" rows="3" required placeholder="Tulis klarifikasi atau informasi tambahan..." class="s-input min-h-[100px] py-3">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-[13px] text-[#991b1b]">{{ $message }}</p>
                        @enderror
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf" class="text-[13px] text-[#475569] file:mr-3 file:py-2 file:px-3 file:rounded-[6px] file:border file:border-[#0f172a]/20 file:text-[13px] file:font-medium file:bg-white file:text-[#0f172a] cursor-pointer">
                            <button type="submit" class="s-btn-primary">
                                Kirim tanggapan
                            </button>
                        </div>
                    </form>
                @else
                    <p class="mt-5 pt-4 border-t border-[#0f172a]/12 text-[14px] text-[#475569]">
                        Tiket ini berstatus <strong class="text-[#0f172a] font-medium">{{ $report->status_label }}</strong> dan ditutup dari tanggapan baru.
                    </p>
                @endif
            </div>
        @endif
    </div>
@endsection
