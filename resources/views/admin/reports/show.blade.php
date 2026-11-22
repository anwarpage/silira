@extends('layouts.admin')

@section('title', 'Detail ' . $report->ticket_code . ' — Admin SILIRA')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('admin.reports.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Kembali ke Daftar Laporan</a>
            <div class="mt-2 flex flex-wrap items-center gap-2">
                <span class="font-mono text-sm font-bold px-2.5 py-1 rounded-lg bg-slate-900 text-white">{{ $report->ticket_code }}</span>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $report->status_badge_class }}">{{ $report->status_label }}</span>
                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-200 text-slate-800">{{ $report->privacy_label }}</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('tracking.show', $report->ticket_code) }}" target="_blank" class="px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                Lihat Versi Publik &nearr;
            </a>
            <a href="{{ route('admin.reports.edit', $report) }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-100">
                Edit Data
            </a>
            <form action="{{ route('admin.reports.destroy', $report) }}" method="POST" onsubmit="return confirm('Hapus laporan ini secara permanen?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-50 text-rose-700 text-xs font-semibold hover:bg-rose-100 cursor-pointer">Hapus</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Report Details & Timeline --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <p class="text-xs font-semibold text-slate-500">{{ $report->jenis_laporan }} &middot; {{ $report->category?->name ?? '-' }} &middot; {{ $report->created_at->format('d M Y, H:i') }} WIB</p>
                <h1 class="mt-2 text-xl font-extrabold text-slate-900">{{ $report->judul_laporan }}</h1>
                <p class="mt-4 text-sm text-slate-800 leading-relaxed whitespace-pre-line">{{ $report->isi_laporan }}</p>

                @if ($report->tanggal_kejadian || $report->lokasi_kejadian)
                    <div class="mt-4 flex flex-wrap gap-4 text-xs text-slate-600 bg-slate-50 rounded-xl p-3.5">
                        @if ($report->tanggal_kejadian)
                            <span><strong>Tanggal Kejadian:</strong> {{ $report->tanggal_kejadian->format('d M Y') }}</span>
                        @endif
                        @if ($report->lokasi_kejadian)
                            <span><strong>Lokasi:</strong> {{ $report->lokasi_kejadian }}</span>
                        @endif
                    </div>
                @endif

                @if ($report->attachment_path)
                    <div class="mt-5 pt-5 border-t border-slate-100">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Lampiran Bukti</p>
                        @if ($report->isAttachmentImage())
                            <a href="{{ asset('storage/' . $report->attachment_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $report->attachment_path) }}" alt="Lampiran" class="max-h-72 rounded-xl border border-slate-200">
                            </a>
                        @else
                            <a href="{{ asset('storage/' . $report->attachment_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 rounded-lg bg-slate-100 text-slate-800 text-xs font-semibold hover:bg-slate-200">
                                Unduh Lampiran ({{ $report->attachment_name ?? 'Berkas' }})
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Form Tindak Lanjut & Respon --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <h2 class="text-base font-extrabold text-slate-900">Tindak Lanjut &amp; Respon DPM</h2>
                <p class="text-xs text-slate-500 mt-0.5">Respon resmi akan langsung muncul pada halaman lacak tiket pelapor. Centang Catatan Internal bila hanya untuk koordinasi internal DPM.</p>

                <form action="{{ route('admin.reports.respond', $report) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">Perbarui Status Laporan</label>
                            <select name="status" id="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-600">
                                @foreach (\App\Models\Report::STATUSES as $k => $label)
                                    <option value="{{ $k }}" {{ $report->status === $k ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-end pb-2">
                            <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                <input type="checkbox" name="is_internal" value="1" class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                                Simpan sebagai Catatan Internal DPM (tidak tampil ke pelapor)
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="message" class="block text-xs font-semibold text-slate-700 mb-1">Pesan Tindak Lanjut <span class="text-red-600">*</span></label>
                        <textarea name="message" id="message" rows="4" required placeholder="Tulis progres verifikasi, hasil audiensi, atau jawaban resmi DPM..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-600">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf" class="text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700">
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-red-700 text-white text-xs font-bold hover:bg-red-800 transition cursor-pointer">
                            Simpan Tindak Lanjut
                        </button>
                    </div>
                </form>
            </div>

            {{-- Full Timeline --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <h2 class="text-base font-extrabold text-slate-900">Seluruh Riwayat Interaksi &amp; Catatan</h2>

                <div class="mt-4 space-y-3">
                    @foreach ($report->responses as $res)
                        <div class="rounded-xl border p-4 {{ $res->is_internal ? 'border-amber-200 bg-amber-50/60' : ($res->sender_type === 'admin' ? 'border-red-100 bg-red-50/30' : 'border-slate-200 bg-slate-50/60') }}">
                            <div class="flex flex-wrap items-center justify-between gap-2 text-xs">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-bold text-slate-900">{{ $res->sender_name }}</span>
                                    @if ($res->is_internal)
                                        <span class="px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 text-[11px] font-bold">Catatan Internal DPM</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 text-[11px] font-semibold">{{ ucfirst($res->sender_type) }}</span>
                                    @endif
                                    @if ($res->status_to)
                                        <span class="text-[11px] font-semibold text-slate-600">&rarr; {{ \App\Models\Report::STATUSES[$res->status_to] ?? $res->status_to }}</span>
                                    @endif
                                </div>
                                <time class="text-slate-500">{{ $res->created_at->format('d M Y, H:i') }}</time>
                            </div>
                            <p class="mt-2 text-sm text-slate-800 whitespace-pre-line">{{ $res->message }}</p>
                            @if ($res->attachment_path)
                                <a href="{{ asset('storage/' . $res->attachment_path) }}" target="_blank" class="mt-2 inline-block text-xs font-semibold text-red-700 hover:underline">
                                    Lihat Lampiran ({{ $res->attachment_name ?? 'Berkas' }})
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right Column: Reporter Info --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <h2 class="text-sm font-extrabold text-slate-900">Identitas Pelapor</h2>
                <p class="text-[11px] text-slate-500 mt-0.5">Hanya terlihat oleh Admin DPM untuk keperluan verifikasi.</p>

                <dl class="mt-4 space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-slate-500">Nama Lengkap</dt>
                        <dd class="font-semibold text-slate-900">{{ $report->nama_pelapor }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">NIM / NIP</dt>
                        <dd class="font-medium text-slate-800">{{ $report->nim_nip }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">Jurusan / Instansi</dt>
                        <dd class="font-medium text-slate-800">{{ $report->jurusan_instansi ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-slate-500">No. WhatsApp</dt>
                        <dd class="font-medium text-slate-800">{{ $report->no_wa }}</dd>
                    </div>
                </dl>

                <a href="{{ $report->whatsapp_url }}" target="_blank" class="mt-5 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 transition">
                    Hubungi via WhatsApp &nearr;
                </a>
            </div>
        </div>
    </div>
@endsection
