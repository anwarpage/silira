@extends('layouts.admin')

@section('title', 'Detail ' . $report->ticket_code . ' — Admin SILIRA')

@section('content')
    @php
        $statusOptions = [
            ['value' => 'menunggu', 'label' => 'Menunggu Verifikasi', 'description' => 'Laporan baru masuk dan belum diperiksa.', 'dot' => 'bg-amber-500'],
            ['value' => 'diverifikasi', 'label' => 'Diverifikasi', 'description' => 'Data valid dan siap diteruskan ke unit terkait.', 'dot' => 'bg-sky-500'],
            ['value' => 'diproses', 'label' => 'Sedang Diproses', 'description' => 'Dalam tahap audiensi atau penanganan fakultas.', 'dot' => 'bg-indigo-500'],
            ['value' => 'selesai', 'label' => 'Selesai', 'description' => 'Laporan tuntas dan ditutup dari balasan baru.', 'dot' => 'bg-emerald-600'],
            ['value' => 'ditolak', 'label' => 'Ditolak', 'description' => 'Tidak dapat dilanjutkan (sertakan alasan).', 'dot' => 'bg-rose-500'],
        ];
    @endphp

    <div class="flex flex-wrap items-center justify-between gap-4 mb-5">
        <div>
            <a href="{{ route('admin.reports.index') }}" class="text-xs text-stone-500 hover:text-stone-900">&larr; Laporan Masuk</a>
            <div class="mt-1.5 flex flex-wrap items-center gap-2 text-xs">
                <span class="font-mono font-semibold px-2 py-0.5 rounded bg-stone-900 text-white">{{ $report->ticket_code }}</span>
                <span class="px-2 py-0.5 rounded-md font-medium {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
                <span class="text-stone-500">{{ $report->privacy_label }}</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('tracking.show', $report->ticket_code) }}" target="_blank" class="btn-press px-3 py-1.5 rounded-lg border border-stone-300 bg-white text-xs font-medium text-stone-700 hover:bg-stone-50">
                Versi Publik &nearr;
            </a>
            <a href="{{ route('admin.reports.edit', $report) }}" class="btn-press px-3 py-1.5 rounded-lg border border-stone-300 bg-white text-xs font-medium text-stone-700 hover:bg-stone-50">
                Edit
            </a>
            <form action="{{ route('admin.reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Hapus laporan ini secara permanen?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-press px-3 py-1.5 rounded-lg text-xs font-medium text-rose-700 hover:bg-rose-50 cursor-pointer">Hapus</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        <div class="lg:col-span-8 space-y-5">
            <div class="bg-white rounded-xl border border-stone-200 p-6">
                <p class="text-xs text-stone-500">{{ $report->jenis_laporan }} &middot; {{ $report->category?->name ?? '-' }} &middot; {{ $report->created_at->format('d M Y, H:i') }} WIB</p>
                <h1 class="mt-2 text-lg font-bold tracking-tight text-stone-950">{{ $report->judul_laporan }}</h1>
                <p class="mt-3.5 text-sm text-stone-800 leading-relaxed whitespace-pre-line">{{ $report->isi_laporan }}</p>

                @if ($report->tanggal_kejadian || $report->lokasi_kejadian)
                    <div class="mt-4 flex flex-wrap gap-4 text-xs text-stone-600 bg-stone-50 rounded-lg px-3.5 py-2.5 border border-stone-100">
                        @if ($report->tanggal_kejadian)
                            <span><strong>Tanggal:</strong> {{ $report->tanggal_kejadian->format('d M Y') }}</span>
                        @endif
                        @if ($report->lokasi_kejadian)
                            <span><strong>Lokasi:</strong> {{ $report->lokasi_kejadian }}</span>
                        @endif
                    </div>
                @endif

                @if ($report->attachment_path)
                    <div class="mt-5 pt-4 border-t border-stone-100">
                        @if ($report->isAttachmentImage())
                            <a href="{{ asset('storage/' . $report->attachment_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $report->attachment_path) }}" alt="Lampiran" class="max-h-64 rounded-lg border border-stone-200">
                            </a>
                        @else
                            <a href="{{ asset('storage/' . $report->attachment_path) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-stone-100 text-stone-800 text-xs font-medium hover:bg-stone-200">
                                Unduh Lampiran ({{ $report->attachment_name ?? 'Berkas' }})
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Form Tindak Lanjut --}}
            <div class="bg-white rounded-xl border border-stone-200 p-6">
                <h2 class="text-sm font-semibold text-stone-950">Tindak Lanjut &amp; Respon DPM</h2>
                <p class="text-xs text-stone-500 mt-0.5">Respon resmi tampil di halaman lacak pelapor. Catatan internal hanya terlihat oleh admin DPM.</p>

                <form action="{{ route('admin.reports.respond', $report) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-medium text-stone-700 mb-1">Status Laporan</label>
                            <x-select-dropdown name="status" :options="$statusOptions" :selected="$report->status" />
                        </div>

                        <label class="flex items-center gap-2 px-3 py-2.5 rounded-lg border border-amber-200 bg-amber-50/50 text-xs font-medium text-amber-900 cursor-pointer">
                            <input type="checkbox" name="is_internal" value="1" class="rounded border-amber-400 text-amber-700 focus:ring-amber-600">
                            Catatan internal DPM saja
                        </label>
                    </div>

                    <div>
                        <label for="message" class="block text-xs font-medium text-stone-700 mb-1">Pesan <span class="text-rose-600">*</span></label>
                        <textarea name="message" id="message" rows="3" required placeholder="Tulis progres verifikasi, hasil audiensi, atau jawaban resmi..." class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/15 focus:border-emerald-800">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf" class="text-xs text-stone-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border file:border-stone-200 file:text-xs file:font-medium file:bg-white file:text-stone-700 cursor-pointer">
                        <button type="submit" class="btn-press px-5 py-2 rounded-lg bg-emerald-900 text-white text-xs font-semibold hover:bg-emerald-950 cursor-pointer">
                            Simpan Tindak Lanjut
                        </button>
                    </div>
                </form>
            </div>

            {{-- Timeline --}}
            <div class="bg-white rounded-xl border border-stone-200 p-6">
                <h2 class="text-sm font-semibold text-stone-950">Riwayat Interaksi</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($report->responses as $res)
                        <div class="rounded-lg border p-4 {{ $res->is_internal ? 'border-amber-200 bg-amber-50/50' : ($res->sender_type === 'admin' ? 'border-emerald-200/70 bg-emerald-50/25' : 'border-stone-200 bg-stone-50/50') }}">
                            <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-semibold text-stone-900">{{ $res->sender_name }}</span>
                                    @if ($res->is_internal)
                                        <span class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-900 text-[11px] font-medium">Internal DPM</span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded bg-white border border-stone-200 text-stone-600 text-[11px]">{{ ucfirst($res->sender_type) }}</span>
                                    @endif
                                    @if ($res->status_to)
                                        <span class="text-[11px] font-medium text-emerald-900">&rarr; {{ \App\Models\Report::STATUSES[$res->status_to] ?? $res->status_to }}</span>
                                    @endif
                                </div>
                                <time class="font-mono text-[11px] text-stone-500">{{ $res->created_at->format('d M Y, H:i') }}</time>
                            </div>
                            <p class="mt-2 text-sm text-stone-800 whitespace-pre-line">{{ $res->message }}</p>
                            @if ($res->attachment_path)
                                <a href="{{ asset('storage/' . $res->attachment_path) }}" target="_blank" class="mt-2 inline-block text-xs font-medium text-emerald-900 hover:underline">
                                    Lampiran: {{ $res->attachment_name ?? 'Berkas' }}
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="lg:col-span-4">
            <div class="bg-white rounded-xl border border-stone-200 p-5 sticky top-6">
                <h2 class="text-xs font-semibold text-stone-950">Identitas Pelapor</h2>
                <dl class="mt-3.5 divide-y divide-stone-100 text-xs">
                    <div class="py-2.5">
                        <dt class="text-stone-500">Nama</dt>
                        <dd class="font-semibold text-stone-900 mt-0.5">{{ $report->nama_pelapor }}</dd>
                    </div>
                    <div class="py-2.5">
                        <dt class="text-stone-500">NIM / NIP</dt>
                        <dd class="font-mono text-stone-800 mt-0.5">{{ $report->nim_nip }}</dd>
                    </div>
                    <div class="py-2.5">
                        <dt class="text-stone-500">Jurusan / Instansi</dt>
                        <dd class="text-stone-800 mt-0.5">{{ $report->jurusan_instansi ?? '-' }}</dd>
                    </div>
                    <div class="py-2.5">
                        <dt class="text-stone-500">WhatsApp</dt>
                        <dd class="font-mono text-stone-800 mt-0.5">{{ $report->no_wa }}</dd>
                    </div>
                </dl>

                <a href="{{ $report->whatsapp_url }}" target="_blank" class="btn-press mt-4 w-full inline-flex items-center justify-center px-4 py-2 rounded-lg border border-stone-300 bg-white text-stone-800 text-xs font-semibold hover:bg-stone-50">
                    Hubungi via WhatsApp &nearr;
                </a>
            </div>
        </div>
    </div>
@endsection
