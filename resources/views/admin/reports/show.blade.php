@extends('layouts.admin')

@section('title', 'Detail ' . $report->ticket_code . ' — SILIRA')

@section('content')
    @php
        $statusOptions = [
            ['value' => 'menunggu', 'label' => 'Menunggu Verifikasi', 'description' => 'Laporan baru masuk dan belum diperiksa.', 'dot' => 'bg-[#d97706]'],
            ['value' => 'diverifikasi', 'label' => 'Diverifikasi', 'description' => 'Data valid dan siap diteruskan ke unit terkait.', 'dot' => 'bg-[#0284c7]'],
            ['value' => 'diproses', 'label' => 'Sedang Diproses', 'description' => 'Dalam tahap audiensi atau penanganan fakultas.', 'dot' => 'bg-[#1e3a8a]'],
            ['value' => 'selesai', 'label' => 'Selesai', 'description' => 'Laporan tuntas dan ditutup dari balasan baru.', 'dot' => 'bg-[#16a34a]'],
            ['value' => 'ditolak', 'label' => 'Ditolak', 'description' => 'Tidak dapat dilanjutkan (sertakan alasan).', 'dot' => 'bg-[#b91c1c]'],
        ];
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-4 mb-6 pb-5 border-b border-[#e5e7eb]">
        <div>
            <a href="{{ route('admin.reports.index') }}" class="text-[14px] text-[#475569] hover:text-[#1e3a8a]">&larr; Laporan masuk</a>
            <div class="mt-2 flex flex-wrap items-center gap-2.5 text-[13px]">
                <span class="font-mono font-medium px-2.5 py-0.5 rounded-[6px] bg-[#0f172a] text-[#ffffff]">{{ $report->ticket_code }}</span>
                <span class="px-2.5 py-0.5 rounded-[6px] font-medium {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
                <span class="text-[#475569]">{{ $report->privacy_label }}</span>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('tracking.show', $report->ticket_code) }}" target="_blank" rel="noopener noreferrer" class="s-btn-secondary h-[40px] px-4 text-[14px]">
                Versi Publik &nearr;
            </a>
            <a href="{{ route('admin.reports.edit', $report) }}" class="s-btn-secondary h-[40px] px-4 text-[14px]">
                Edit
            </a>
            <form action="{{ route('admin.reports.destroy', $report) }}" method="POST" data-confirm-title="Hapus laporan {{ $report->ticket_code }}?" data-confirm-message="Laporan beserta seluruh riwayat tanggapan dan lampirannya akan dihapus secara permanen dan tidak dapat dipulihkan.">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 h-[40px] rounded-[9px] text-[14px] font-medium text-[#991b1b] hover:bg-[#fee2e2] cursor-pointer">Hapus</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8 space-y-6">
            <div class="s-card-accent p-6 sm:p-7">
                <p class="text-[13px] text-[#475569]">{{ $report->jenis_laporan }} &middot; {{ $report->category?->name ?? '-' }} &middot; {{ $report->created_at->format('d M Y, H:i') }} WIB</p>
                <h1 class="mt-2 text-[24px] font-semibold leading-snug text-[#0f172a] break-words [overflow-wrap:anywhere]">{{ $report->judul_laporan }}</h1>
                <p class="mt-4 text-[16px] text-[#0f172a] leading-relaxed whitespace-pre-line break-words [overflow-wrap:anywhere]">{{ $report->isi_laporan }}</p>

                @if ($report->tanggal_kejadian || $report->lokasi_kejadian)
                    <div class="mt-5 flex flex-wrap gap-5 text-[14px] text-[#475569] bg-[#f8fafc] border border-[#e5e7eb] rounded-[9px] px-4 py-3">
                        @if ($report->tanggal_kejadian)
                            <span><strong class="text-[#0f172a] font-medium">Tanggal:</strong> {{ $report->tanggal_kejadian->format('d M Y') }}</span>
                        @endif
                        @if ($report->lokasi_kejadian)
                            <span><strong class="text-[#0f172a] font-medium">Lokasi:</strong> {{ $report->lokasi_kejadian }}</span>
                        @endif
                    </div>
                @endif

                @if ($report->attachment_path)
                    <div class="mt-5 pt-5 border-t border-[#e5e7eb]">
                        @if ($report->isAttachmentImage())
                            <a href="{{ route('admin.reports.attachment', $report) }}" target="_blank" rel="noopener noreferrer">
                                <img src="{{ route('admin.reports.attachment', $report) }}" alt="Lampiran" class="max-h-64 rounded-[9px] border border-[#e5e7eb]">
                            </a>
                        @else
                            <a href="{{ route('admin.reports.attachment', $report) }}" target="_blank" rel="noopener noreferrer" class="s-btn-secondary h-[40px] text-[14px]">
                                Unduh lampiran ({{ $report->attachment_name ?? 'Berkas' }})
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Form Tindak Lanjut --}}
            <div class="s-card p-6 sm:p-7">
                <h2 class="font-sans text-[17px] font-semibold text-[#0f172a]">Tindak lanjut &amp; respon DPM</h2>
                <p class="text-[14px] text-[#475569] mt-1">Respon resmi tampil di halaman lacak pelapor. Catatan internal hanya terlihat oleh admin DPM.</p>

                <form action="{{ route('admin.reports.respond', $report) }}" method="POST" enctype="multipart/form-data" class="mt-5 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
                        <div>
                            <label class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Status laporan</label>
                            <x-select-dropdown name="status" :options="$statusOptions" :selected="$report->status" />
                        </div>

                        <label class="flex items-center gap-2.5 px-3.5 h-[46px] rounded-[9px] border border-[#d97706]/40 bg-[#fbf6ea] text-[14px] font-medium text-[#8a6116] cursor-pointer">
                            <input type="checkbox" name="is_internal" value="1" class="rounded-[6px] accent-[#8a6116]">
                            Catatan internal DPM saja
                        </label>
                    </div>

                    <div>
                        <label for="message" class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Pesan tindak lanjut</label>
                        <textarea name="message" id="message" rows="3" required placeholder="Tulis progres verifikasi, hasil audiensi, atau jawaban resmi..." class="s-input min-h-[110px] py-3">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-[13px] text-[#991b1b]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <label data-dropzone class="flex-1 flex items-center gap-2.5 rounded-[9px] border border-dashed border-[#d0d7de] bg-[#f8fafc] px-3.5 py-2.5 hover:border-[#1e3a8a] transition cursor-pointer min-w-0">
                            <svg class="w-4 h-4 text-[#475569] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m18.375 12.739-7.693 7.693a4.5 4.5 0 0 1-6.364-6.364l10.94-10.94A3 3 0 1 1 19.5 7.372L8.552 18.32m.009-.01-.01.01m5.699-9.941-7.81 7.81a1.5 1.5 0 0 0 2.112 2.13" /></svg>
                            <span data-dropzone-filename data-default-text="Lampirkan berkas opsional (JPG, PNG, WEBP, PDF maks. 5 MB)" class="text-[13px] text-[#475569] truncate">Lampirkan berkas opsional (JPG, PNG, WEBP, PDF maks. 5 MB)</span>
                            <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf" class="sr-only">
                        </label>
                        <button type="submit" data-loading-text="Menyimpan..." class="s-btn-primary shrink-0">
                            Simpan Tindak Lanjut
                        </button>
                    </div>
                    @error('attachment')
                        <p class="text-[13px] text-[#991b1b]">{{ $message }}</p>
                    @enderror
                </form>
            </div>

            {{-- Timeline --}}
            <div class="s-card p-6 sm:p-7">
                <h2 class="font-sans text-[17px] font-semibold text-[#0f172a]">Riwayat interaksi</h2>
                <div class="mt-4 divide-y divide-[#e5e7eb]">
                    @foreach ($report->responses as $res)
                        <div class="py-4 first:pt-0">
                            <div class="flex flex-wrap items-center justify-between gap-2 text-[13px]">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-semibold text-[#0f172a]">{{ $res->sender_name }}</span>
                                    @if ($res->is_internal)
                                        <span class="px-2 py-0.5 rounded-[6px] bg-[#fef3c7] text-[#92400e] font-medium">Internal DPM</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-[6px] bg-[#f8fafc] text-[#475569]">{{ ucfirst($res->sender_type) }}</span>
                                    @endif
                                    @if ($res->status_to)
                                        <span class="font-medium text-[#1e3a8a]">&rarr; {{ \App\Models\Report::STATUSES[$res->status_to] ?? $res->status_to }}</span>
                                    @endif
                                </div>
                                <time class="font-mono text-[#475569]">{{ $res->created_at->format('d M Y, H:i') }}</time>
                            </div>
                            <p class="mt-2 text-[15px] text-[#0f172a] leading-relaxed whitespace-pre-line break-words [overflow-wrap:anywhere]">{{ $res->message }}</p>
                            @if ($res->attachment_path)
                                <a href="{{ route('admin.responses.attachment', $res) }}" target="_blank" rel="noopener noreferrer" class="mt-2 inline-block text-[14px] s-link">
                                    Lampiran: {{ $res->attachment_name ?? 'Berkas' }}
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="lg:col-span-4">
            <div class="s-card p-6 sticky top-24">
                <h2 class="font-sans text-[16px] font-semibold text-[#0f172a] pb-3 border-b border-[#e5e7eb]">Identitas pelapor</h2>
                <dl class="mt-2 divide-y divide-[#e5e7eb] text-[14px]">
                    <div class="py-3">
                        <dt class="text-[12.5px] text-[#475569]">Nama</dt>
                        <dd class="font-medium text-[#0f172a] mt-0.5">{{ $report->nama_pelapor }}</dd>
                    </div>
                    <div class="py-3">
                        <dt class="text-[12.5px] text-[#475569]">NIM / NIP</dt>
                        <dd class="font-mono text-[#0f172a] mt-0.5">{{ $report->nim_nip }}</dd>
                    </div>
                    <div class="py-3">
                        <dt class="text-[12.5px] text-[#475569]">Jurusan / instansi</dt>
                        <dd class="text-[#0f172a] mt-0.5">{{ $report->jurusan_instansi ?? '-' }}</dd>
                    </div>
                    <div class="py-3">
                        <dt class="text-[12.5px] text-[#475569]">WhatsApp</dt>
                        <dd class="font-mono text-[#0f172a] mt-0.5">{{ $report->no_wa }}</dd>
                    </div>
                </dl>

                @if ($report->whatsapp_url)
                    <a href="{{ $report->whatsapp_url }}" target="_blank" rel="noopener noreferrer" class="s-btn-primary w-full mt-5">
                        Hubungi via WhatsApp &nearr;
                    </a>
                @endif
            </div>
        </div>
    </div>
@endsection
