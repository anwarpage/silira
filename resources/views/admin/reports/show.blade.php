@extends('layouts.admin')

@section('title', 'Detail ' . $report->ticket_code . ' — Silira')

@section('content')
    @php
        $statusOptions = [
            ['value' => 'menunggu', 'label' => 'Menunggu Verifikasi', 'description' => 'Laporan baru masuk dan belum diperiksa.', 'dot' => 'bg-[#d97706]'],
            ['value' => 'diverifikasi', 'label' => 'Diverifikasi', 'description' => 'Data valid dan siap diteruskan ke unit terkait.', 'dot' => 'bg-[#0284c7]'],
            ['value' => 'diproses', 'label' => 'Sedang Diproses', 'description' => 'Dalam tahap audiensi atau penanganan fakultas.', 'dot' => 'bg-[#1e3a8a]'],
            ['value' => 'selesai', 'label' => 'Selesai', 'description' => 'Laporan tuntas dan ditutup dari balasan baru.', 'dot' => 'bg-[#1e3a8a]'],
            ['value' => 'ditolak', 'label' => 'Ditolak', 'description' => 'Tidak dapat dilanjutkan (sertakan alasan).', 'dot' => 'bg-[#dc2626]'],
        ];
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('admin.reports.index') }}" class="text-[14px] text-[#475569] hover:text-[#0f172a]">&larr; Laporan masuk</a>
            <div class="mt-2 flex flex-wrap items-center gap-2.5 text-[13px]">
                <span class="font-mono font-medium px-2.5 py-0.5 rounded-[6px] bg-[#0f172a] text-[#f5f7fa]">{{ $report->ticket_code }}</span>
                <span class="px-2.5 py-0.5 rounded-[6px] font-medium {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
                <span class="text-[#475569]">{{ $report->privacy_label }}</span>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('tracking.show', $report->ticket_code) }}" target="_blank" class="s-btn-secondary h-[40px] px-4 text-[14px]">
                Versi publik &nearr;
            </a>
            <a href="{{ route('admin.reports.edit', $report) }}" class="s-btn-secondary h-[40px] px-4 text-[14px]">
                Edit
            </a>
            <form action="{{ route('admin.reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Hapus laporan ini secara permanen?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 h-[40px] rounded-[8px] text-[14px] font-medium text-[#991b1b] hover:bg-[#fee2e2] cursor-pointer">Hapus</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-8 space-y-6">
            <div class="s-card p-6 sm:p-7">
                <p class="text-[13px] text-[#475569]">{{ $report->jenis_laporan }} &middot; {{ $report->category?->name ?? '-' }} &middot; {{ $report->created_at->format('d M Y, H:i') }} WIB</p>
                <h1 class="mt-2 text-[22px] font-medium leading-snug text-[#0f172a]">{{ $report->judul_laporan }}</h1>
                <p class="mt-4 text-[16px] text-[#0f172a] leading-relaxed whitespace-pre-line">{{ $report->isi_laporan }}</p>

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
                    <div class="mt-5 pt-5 border-t border-[#0f172a]/12">
                        @if ($report->isAttachmentImage())
                            <a href="{{ asset('storage/' . $report->attachment_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $report->attachment_path) }}" alt="Lampiran" class="max-h-64 rounded-[8px] border border-[#0f172a]/15">
                            </a>
                        @else
                            <a href="{{ asset('storage/' . $report->attachment_path) }}" target="_blank" class="s-btn-secondary h-[40px] text-[14px]">
                                Unduh lampiran ({{ $report->attachment_name ?? 'Berkas' }})
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Form Tindak Lanjut --}}
            <div class="s-card p-6 sm:p-7">
                <h2 class="text-[17px] font-medium text-[#0f172a]">Tindak lanjut &amp; respon DPM</h2>
                <p class="text-[14px] text-[#475569] mt-1">Respon resmi tampil di halaman lacak pelapor. Catatan internal hanya terlihat oleh admin DPM.</p>

                <form action="{{ route('admin.reports.respond', $report) }}" method="POST" enctype="multipart/form-data" class="mt-5 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
                        <div>
                            <label class="block text-[13px] font-medium text-[#475569] mb-1.5">Status laporan</label>
                            <x-select-dropdown name="status" :options="$statusOptions" :selected="$report->status" />
                        </div>

                        <label class="flex items-center gap-2.5 px-3.5 h-[48px] rounded-[8px] border border-[#0f172a]/20 bg-[#fef3c7]/60 text-[14px] font-medium text-[#92400e] cursor-pointer">
                            <input type="checkbox" name="is_internal" value="1" class="rounded accent-[#92400e]">
                            Catatan internal DPM saja
                        </label>
                    </div>

                    <div>
                        <label for="message" class="block text-[13px] font-medium text-[#475569] mb-1.5">Pesan tindak lanjut</label>
                        <textarea name="message" id="message" rows="3" required placeholder="Tulis progres verifikasi, hasil audiensi, atau jawaban resmi..." class="s-input min-h-[110px] py-3">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-[13px] text-[#991b1b]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf" class="text-[13px] text-[#475569] file:mr-3 file:py-2 file:px-3 file:rounded-[6px] file:border file:border-[#0f172a]/20 file:text-[13px] file:font-medium file:bg-white file:text-[#0f172a] cursor-pointer">
                        <button type="submit" class="s-btn-primary">
                            Simpan tindak lanjut
                        </button>
                    </div>
                </form>
            </div>

            {{-- Timeline --}}
            <div class="s-card p-6 sm:p-7">
                <h2 class="text-[17px] font-medium text-[#0f172a]">Riwayat interaksi</h2>
                <div class="mt-4 divide-y divide-[#0f172a]/12">
                    @foreach ($report->responses as $res)
                        <div class="py-4 first:pt-0">
                            <div class="flex flex-wrap items-center justify-between gap-2 text-[13px]">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-medium text-[#0f172a]">{{ $res->sender_name }}</span>
                                    @if ($res->is_internal)
                                        <span class="px-2 py-0.5 rounded-[6px] bg-[#fef3c7] text-[#92400e] font-medium">Internal DPM</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-[6px] bg-[#f5f7fa] text-[#475569]">{{ ucfirst($res->sender_type) }}</span>
                                    @endif
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
                    @endforeach
                </div>
            </div>
        </div>

        <div class="lg:col-span-4">
            <div class="s-card p-6 sticky top-8">
                <h2 class="text-[16px] font-medium text-[#0f172a]">Identitas pelapor</h2>
                <dl class="mt-4 divide-y divide-[#0f172a]/10 text-[14px]">
                    <div class="py-3">
                        <dt class="text-[13px] text-[#475569]">Nama</dt>
                        <dd class="font-medium text-[#0f172a] mt-0.5">{{ $report->nama_pelapor }}</dd>
                    </div>
                    <div class="py-3">
                        <dt class="text-[13px] text-[#475569]">NIM / NIP</dt>
                        <dd class="font-mono text-[#0f172a] mt-0.5">{{ $report->nim_nip }}</dd>
                    </div>
                    <div class="py-3">
                        <dt class="text-[13px] text-[#475569]">Jurusan / instansi</dt>
                        <dd class="text-[#0f172a] mt-0.5">{{ $report->jurusan_instansi ?? '-' }}</dd>
                    </div>
                    <div class="py-3">
                        <dt class="text-[13px] text-[#475569]">WhatsApp</dt>
                        <dd class="font-mono text-[#0f172a] mt-0.5">{{ $report->no_wa }}</dd>
                    </div>
                </dl>

                <a href="{{ $report->whatsapp_url }}" target="_blank" class="s-btn-primary w-full mt-5">
                    Hubungi via WhatsApp &nearr;
                </a>
            </div>
        </div>
    </div>
@endsection
