@extends('layouts.admin')

@section('title', 'Edit ' . $report->ticket_code . ' — Admin SILIRA')

@section('content')
    @php
        $jenisOptions = collect(\App\Models\Report::JENIS_OPTIONS)->map(fn ($j) => ['value' => $j, 'label' => $j])->all();
        $categoryOptions = $categories->map(fn ($c) => ['value' => $c->id, 'label' => $c->name, 'description' => $c->description])->all();
        $privacyOptions = [
            ['value' => 'public', 'label' => 'Publik', 'dot' => 'bg-emerald-500'],
            ['value' => 'anonymous', 'label' => 'Anonim', 'dot' => 'bg-amber-500'],
            ['value' => 'confidential', 'label' => 'Rahasia', 'dot' => 'bg-rose-600'],
        ];
        $statusDots = [
            'menunggu' => 'bg-amber-500',
            'diverifikasi' => 'bg-sky-500',
            'diproses' => 'bg-indigo-500',
            'selesai' => 'bg-emerald-500',
            'ditolak' => 'bg-rose-500',
        ];
        $statusOptions = collect(\App\Models\Report::STATUSES)->map(fn ($label, $key) => [
            'value' => $key,
            'label' => $label,
            'dot' => $statusDots[$key] ?? null,
        ])->values()->all();
    @endphp

    <div class="max-w-3xl">
        <a href="{{ route('admin.reports.show', $report) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-900">&larr; Kembali ke Detail Laporan</a>
        <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-950">Edit Laporan <span class="font-mono">{{ $report->ticket_code }}</span></h1>

        @if ($errors->any())
            <div class="mt-4 rounded-xl bg-rose-50 border border-rose-200 p-4 text-xs text-rose-800">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.reports.update', $report) }}" method="POST" class="mt-6 bg-white rounded-2xl border border-slate-200/90 p-6 sm:p-7 space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Klasifikasi</label>
                    <x-select-dropdown name="jenis_laporan" :options="$jenisOptions" :selected="$report->jenis_laporan" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori Bidang</label>
                    <x-select-dropdown name="category_id" :options="$categoryOptions" :selected="$report->category_id" :searchable="true" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Privasi</label>
                    <x-select-dropdown name="privacy" :options="$privacyOptions" :selected="$report->privacy" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Status</label>
                    <x-select-dropdown name="status" :options="$statusOptions" :selected="$report->status" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Pelapor</label>
                    <input type="text" name="nama_pelapor" value="{{ old('nama_pelapor', $report->nama_pelapor) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300/90 text-sm focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">NIM / NIP</label>
                    <input type="text" name="nim_nip" value="{{ old('nim_nip', $report->nim_nip) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300/90 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jurusan / Instansi</label>
                    <input type="text" name="jurusan_instansi" value="{{ old('jurusan_instansi', $report->jurusan_instansi) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300/90 text-sm focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">No. WA</label>
                    <input type="text" name="no_wa" value="{{ old('no_wa', $report->no_wa) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300/90 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Judul Laporan</label>
                <input type="text" name="judul_laporan" value="{{ old('judul_laporan', $report->judul_laporan) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300/90 text-sm focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Isi Laporan</label>
                <textarea name="isi_laporan" rows="5" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300/90 text-sm leading-relaxed focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600">{{ old('isi_laporan', $report->isi_laporan) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Kejadian</label>
                    <input type="date" name="tanggal_kejadian" value="{{ old('tanggal_kejadian', $report->tanggal_kejadian?->format('Y-m-d')) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300/90 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Lokasi Kejadian</label>
                    <input type="text" name="lokasi_kejadian" value="{{ old('lokasi_kejadian', $report->lokasi_kejadian) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300/90 text-sm">
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('admin.reports.show', $report) }}" class="btn-press px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-100">Batal</a>
                <button type="submit" class="btn-press px-6 py-2.5 rounded-xl bg-red-700 text-white text-xs font-bold hover:bg-red-800 cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
@endsection
