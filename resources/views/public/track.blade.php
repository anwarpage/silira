@extends('layouts.public')

@section('title', $report ? "Tiket {$report->ticket_code} — SILIRA" : 'Lacak Tiket — SILIRA')

@section('content')
    {{-- Institutional Page Header Band --}}
    <div class="bg-white border-b border-[#e5e7eb]">
        <div class="max-w-[1180px] mx-auto px-5 sm:px-8 py-9">
            <nav aria-label="Breadcrumb" class="text-[13px] text-[#475569] mb-3">
                <a href="{{ route('home') }}" class="hover:text-[#1e3a8a] transition">Beranda</a>
                <span class="mx-2 text-[#d0d7de]" aria-hidden="true">/</span>
                <span class="text-[#0f172a] font-medium">Lacak Tiket</span>
            </nav>

            <h1 class="text-[30px] sm:text-[36px] font-semibold leading-tight text-[#0f172a]">
                Lacak Status Tiket Laporan
            </h1>
            <p class="mt-2 text-[15.5px] text-[#475569] max-w-2xl">
                Masukkan nomor tiket resmi untuk memantau progres verifikasi, membaca tanggapan DPM, dan mengirim klarifikasi tambahan.
            </p>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-5 sm:px-8 py-9">
        <div class="s-card-accent p-6 sm:p-7">
            <form action="{{ route('tracking.lookup') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <div class="flex-1">
                    <label for="ticket_code_input" class="sr-only">Nomor tiket</label>
                    <input
                        type="text"
                        id="ticket_code_input"
                        name="ticket_code"
                        value="{{ old('ticket_code', $searchedCode) }}"
                        placeholder="Contoh: SLR-2026-FDIK01"
                        class="s-input font-mono uppercase placeholder:normal-case placeholder:font-sans"
                        required
                    >
                </div>
                <button type="submit" class="s-btn-primary h-[46px] shrink-0">
                    Cari Tiket
                </button>
            </form>
        </div>

        @if (session('success'))
            <div class="mt-5 rounded-[9px] bg-[#eff6ff] border border-[#1e3a8a]/35 px-4 py-3 text-[14px] font-medium text-[#1e3a8a]">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mt-5 rounded-[9px] bg-[#fee2e2] border border-[#b91c1c]/35 px-4 py-3 text-[14px] font-medium text-[#991b1b]">
                {{ session('error') }}
            </div>
        @endif

        @if ($searchedCode && ! $report)
            <div class="mt-6 s-card p-8 text-center">
                <p class="text-[16px] font-semibold text-[#0f172a]">Nomor tiket <span class="font-mono text-[#991b1b]">{{ $searchedCode }}</span> tidak ditemukan.</p>
                <p class="mt-1.5 text-[14.5px] text-[#475569]">Pastikan penulisan nomor tiket sesuai dengan yang tercantum pada tanda terima laporan Anda.</p>
            </div>
        @endif

        @if ($report && $isLockedConfidential)
            <div class="mt-6 s-card p-6 sm:p-8">
                <div class="flex flex-wrap items-center gap-2.5 text-[13px]">
                    <span class="font-mono font-medium px-2.5 py-0.5 rounded-[6px] bg-[#0f172a] text-[#ffffff]">{{ $report->ticket_code }}</span>
                    <span class="px-2.5 py-0.5 rounded-[6px] font-medium bg-[#fee2e2] text-[#991b1b] border border-[#b91c1c]/30">Laporan Rahasia</span>
                </div>

                <h2 class="mt-4 text-[24px] font-semibold text-[#0f172a]">Verifikasi PIN akses tiket</h2>
                <p class="mt-1.5 text-[15px] text-[#475569]">
                    Berkas ini bersifat <strong class="text-[#0f172a]">Rahasia</strong>. Masukkan PIN akses pribadi yang Anda terima saat mengirim laporan untuk membuka isi dan riwayat tindak lanjut.
                </p>

                <form action="{{ route('tracking.unlock', $report->ticket_code) }}" method="POST" class="mt-5 flex flex-col sm:flex-row gap-3">
                    @csrf
                    <div class="flex-1">
                        <label for="access_pin_unlock" class="sr-only">PIN akses</label>
                        <input
                            type="text"
                            id="access_pin_unlock"
                            name="access_pin"
                            value="{{ old('access_pin') }}"
                            placeholder="Masukkan PIN akses (mis. FDIK26)"
                            class="s-input font-mono uppercase placeholder:normal-case placeholder:font-sans"
                            required
                            autofocus
                        >
                        @error('access_pin')
                            <p class="mt-1.5 text-[13px] text-[#991b1b]">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="s-btn-primary h-[46px] shrink-0">
                        Buka Laporan
                    </button>
                </form>
            </div>
        @elseif ($report)
            @php
                $steps = ['menunggu' => 'Menunggu', 'diverifikasi' => 'Diverifikasi', 'diproses' => 'Diproses', 'selesai' => 'Selesai'];
                $order = ['menunggu' => 1, 'diverifikasi' => 2, 'diproses' => 3, 'selesai' => 4, 'ditolak' => 0];
                $currentStep = $order[$report->status] ?? 1;
            @endphp

            <div class="mt-6 s-card p-6 sm:p-8">
                <div class="flex flex-wrap items-center gap-2.5 text-[13px]">
                    <span class="font-mono font-medium px-2.5 py-0.5 rounded-[6px] bg-[#0f172a] text-[#ffffff]">{{ $report->ticket_code }}</span>
                    <span class="px-2.5 py-0.5 rounded-[6px] font-medium {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
                    <span class="text-[#475569]">{{ $report->jenis_laporan }}</span>
                    <span class="text-[#475569]">&middot; {{ $report->privacy_label }}</span>
                </div>

                <h2 class="mt-4 text-[24px] font-semibold leading-snug text-[#0f172a] break-words [overflow-wrap:anywhere]">{{ $report->judul_laporan }}</h2>
                <p class="mt-1.5 text-[14px] text-[#475569]">
                    {{ $report->public_reporter_name }}
                    @if ($report->category) &middot; {{ $report->category->name }} @endif
                    &middot; {{ $report->created_at->format('d M Y, H:i') }} WIB
                </p>

                @if ($report->status === 'ditolak')
                    <div class="mt-6 rounded-[9px] bg-[#fee2e2] border border-[#b91c1c]/30 p-4 text-[14px] text-[#991b1b]">
                        Laporan ini berstatus Ditolak. Lihat penjelasan resmi dari Admin DPM pada riwayat tindak lanjut di bawah.
                    </div>
                @else
                    <div class="mt-6 grid grid-cols-4 gap-2.5">
                        @foreach ($steps as $key => $label)
                            @php $done = $currentStep >= $order[$key]; @endphp
                            <div>
                                <div class="h-1.5 rounded-[6px] {{ $done ? 'bg-[#1e3a8a]' : 'bg-[#e5e7eb]' }}"></div>
                                <span class="block mt-2 text-[12px] sm:text-[13px] {{ $done ? 'font-semibold text-[#0f172a]' : 'text-[#475569]' }}">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-6 pt-6 border-t border-[#e5e7eb]">
                    <p class="text-[16px] text-[#0f172a] leading-relaxed whitespace-pre-line break-words [overflow-wrap:anywhere]">{{ $report->isi_laporan }}</p>

                    @if ($report->tanggal_kejadian || $report->lokasi_kejadian)
                        <div class="mt-5 flex flex-wrap gap-x-6 gap-y-2 text-[14px] text-[#475569] bg-[#f8fafc] border border-[#e5e7eb] rounded-[9px] px-4 py-3">
                            @if ($report->tanggal_kejadian)
                                <span><strong class="text-[#0f172a] font-medium">Tanggal:</strong> {{ $report->tanggal_kejadian->format('d M Y') }}</span>
                            @endif
                            @if ($report->lokasi_kejadian)
                                <span class="break-words [overflow-wrap:anywhere]"><strong class="text-[#0f172a] font-medium">Lokasi:</strong> {{ $report->lokasi_kejadian }}</span>
                            @endif
                        </div>
                    @endif

                    @if ($report->attachment_path)
                        <div class="mt-6">
                            <p class="text-[13px] font-medium text-[#475569] mb-2">Lampiran bukti</p>
                            @if ($report->isAttachmentImage())
                                <div class="inline-block rounded-[9px] border border-[#e5e7eb] bg-[#f8fafc] p-2.5 max-w-full">
                                    <a href="{{ route('tracking.attachment', $report->ticket_code) }}" target="_blank" rel="noopener noreferrer" class="block">
                                        <img src="{{ route('tracking.attachment', $report->ticket_code) }}" alt="Lampiran Bukti" class="max-h-72 max-w-full rounded-[6px] object-contain">
                                    </a>
                                    <div class="mt-2 flex items-center justify-between gap-3 px-1 text-[12px] text-[#475569]">
                                        <span class="truncate">{{ $report->attachment_name ?? 'Lampiran gambar' }}</span>
                                        <a href="{{ route('tracking.attachment', $report->ticket_code) }}" target="_blank" rel="noopener noreferrer" class="s-link shrink-0">Buka penuh &nearr;</a>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('tracking.attachment', $report->ticket_code) }}" target="_blank" rel="noopener noreferrer" class="s-btn-secondary h-[40px] text-[14px]">
                                    Unduh lampiran ({{ $report->attachment_name ?? 'Berkas' }})
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Timeline --}}
            <div class="mt-6 s-card p-6 sm:p-8">
                <h3 class="text-[18px] font-semibold text-[#0f172a]">Riwayat tindak lanjut resmi</h3>

                <div class="mt-5 divide-y divide-[#e5e7eb]">
                    @forelse ($report->publicResponses as $res)
                        <div class="py-4 first:pt-0">
                            <div class="flex flex-wrap items-center justify-between gap-2 text-[13px]">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-semibold text-[#0f172a]">{{ $res->sender_type === 'reporter' ? $report->public_reporter_name : $res->sender_name }}</span>
                                    <span class="px-2 py-0.5 rounded-[6px] bg-[#f8fafc] text-[#475569]">
                                        {{ $res->sender_type === 'admin' ? 'Admin DPM' : ($res->sender_type === 'reporter' ? 'Pelapor' : 'Sistem') }}
                                    </span>
                                    @if ($res->status_to)
                                        <span class="font-medium text-[#1e3a8a]">&rarr; {{ \App\Models\Report::STATUSES[$res->status_to] ?? $res->status_to }}</span>
                                    @endif
                                </div>
                                <time class="font-mono text-[#475569]">{{ $res->created_at->format('d M Y, H:i') }}</time>
                            </div>
                            <p class="mt-2 text-[15px] text-[#0f172a] leading-relaxed whitespace-pre-line break-words [overflow-wrap:anywhere]">{{ $res->message }}</p>
                            @if ($res->attachment_path)
                                <a href="{{ route('tracking.response.attachment', [$report->ticket_code, $res]) }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-block text-[14px] s-link">
                                    Lampiran: {{ $res->attachment_name ?? 'Berkas' }}
                                </a>
                            @endif
                        </div>
                    @empty
                        <p class="text-[15px] text-[#475569]">Belum ada riwayat tindak lanjut.</p>
                    @endforelse
                </div>

                @if ($report->canReporterReply())
                    <form action="{{ route('tracking.reply', $report->ticket_code) }}" method="POST" enctype="multipart/form-data" class="mt-6 pt-6 border-t border-[#e5e7eb] space-y-3.5">
                        @csrf
                        <label class="block text-[14.5px] font-semibold text-[#0f172a]">Tambah tanggapan atau bukti pendukung</label>

                        @if (! $isPinUnlocked)
                            <div>
                                <label for="reply_access_pin" class="block text-[13px] font-medium text-[#475569] mb-1">PIN akses tiket (verifikasi pemilik laporan)</label>
                                <input
                                    type="text"
                                    id="reply_access_pin"
                                    name="access_pin"
                                    value="{{ old('access_pin') }}"
                                    required
                                    placeholder="Masukkan PIN akses Anda"
                                    class="s-input s-input-sm font-mono uppercase placeholder:normal-case placeholder:font-sans max-w-xs"
                                >
                                @error('access_pin')
                                    <p class="mt-1 text-[13px] text-[#991b1b]">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <textarea name="message" rows="3" required placeholder="Tulis klarifikasi atau informasi tambahan..." class="s-input min-h-[100px] py-3">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="text-[13px] text-[#991b1b]">{{ $message }}</p>
                        @enderror
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <label data-dropzone class="flex-1 flex items-center gap-2.5 rounded-[9px] border border-dashed border-[#d0d7de] bg-[#f8fafc] px-3.5 py-2.5 hover:border-[#1e3a8a] transition cursor-pointer min-w-0">
                                <svg class="w-4 h-4 text-[#475569] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" /></svg>
                                <span data-dropzone-filename data-default-text="Lampirkan berkas opsional (JPG, PNG, WEBP, PDF maks. 5 MB)" class="text-[13px] text-[#475569] truncate">Lampirkan berkas opsional (JPG, PNG, WEBP, PDF maks. 5 MB)</span>
                                <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf" class="sr-only">
                            </label>
                            <button type="submit" data-loading-text="Mengirim..." class="s-btn-primary shrink-0">
                                Kirim Tanggapan
                            </button>
                        </div>
                        @error('attachment')
                            <p class="text-[13px] text-[#991b1b]">{{ $message }}</p>
                        @enderror
                    </form>
                @else
                    <p class="mt-5 pt-4 border-t border-[#e5e7eb] text-[14px] text-[#475569]">
                        Tiket ini berstatus <strong class="text-[#0f172a] font-medium">{{ $report->status_label }}</strong> dan ditutup dari tanggapan baru.
                    </p>
                @endif
            </div>
        @endif
    </div>
@endsection
