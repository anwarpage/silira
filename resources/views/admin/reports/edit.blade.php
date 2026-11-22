@extends('layouts.admin')

@section('title', 'Edit ' . $report->ticket_code . ' — Admin SILIRA')

@section('content')
    @php
        $jenisOptions = collect(\App\Models\Report::JENIS_OPTIONS)->map(fn ($j) => ['value' => $j, 'label' => $j])->all();
        $categoryOptions = $categories->map(fn ($c) => ['value' => $c->id, 'label' => $c->name, 'description' => $c->description])->all();
        $privacyOptions = [
            ['value' => 'public', 'label' => 'Publik', 'dot' => 'bg-emerald-600'],
            ['value' => 'anonymous', 'label' => 'Anonim', 'dot' => 'bg-amber-500'],
            ['value' => 'confidential', 'label' => 'Rahasia', 'dot' => 'bg-rose-600'],
        ];
        $statusDots = [
            'menunggu' => 'bg-amber-500',
            'diverifikasi' => 'bg-sky-500',
            'diproses' => 'bg-indigo-500',
            'selesai' => 'bg-emerald-600',
            'ditolak' => 'bg-rose-500',
        ];
        $statusOptions = collect(\App\Models\Report::STATUSES)->map(fn ($label, $key) => [
            'value' => $key,
            'label' => $label,
            'dot' => $statusDots[$key] ?? null,
        ])->values()->all();
    @endphp

    <div class="max-w-2xl">
        <a href="{{ route('admin.reports.show', $report) }}" class="text-xs text-stone-500 hover:text-stone-900">&larr; Detail Laporan</a>
        <h1 class="mt-1.5 text-xl font-bold tracking-tight text-stone-950">Edit <span class="font-mono">{{ $report->ticket_code }}</span></h1>

        @if ($errors->any())
            <div class="mt-4 rounded-lg bg-rose-50 border border-rose-200 p-4 text-xs text-rose-900">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.reports.update', $report) }}" method="POST" class="mt-5 bg-white rounded-xl border border-stone-200 p-6 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-stone-700 mb-1">Klasifikasi</label>
                    <x-select-dropdown name="jenis_laporan" :options="$jenisOptions" :selected="$report->jenis_laporan" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-700 mb-1">Kategori Bidang</label>
                    <x-select-dropdown name="category_id" :options="$categoryOptions" :selected="$report->category_id" :searchable="true" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-700 mb-1">Privasi</label>
                    <x-select-dropdown name="privacy" :options="$privacyOptions" :selected="$report->privacy" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-700 mb-1">Status</label>
                    <x-select-dropdown name="status" :options="$statusOptions" :selected="$report->status" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-stone-700 mb-1">Nama Pelapor</label>
                    <input type="text" name="nama_pelapor" value="{{ old('nama_pelapor', $report->nama_pelapor) }}" required class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:border-emerald-800">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-700 mb-1">NIM / NIP</label>
                    <input type="text" name="nim_nip" value="{{ old('nim_nip', $report->nim_nip) }}" required class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm font-mono focus:outline-none focus:border-emerald-800">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-700 mb-1">Jurusan / Instansi</label>
                    <input type="text" name="jurusan_instansi" value="{{ old('jurusan_instansi', $report->jurusan_instansi) }}" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:border-emerald-800">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-700 mb-1">No. WA</label>
                    <input type="text" name="no_wa" value="{{ old('no_wa', $report->no_wa) }}" required class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm font-mono focus:outline-none focus:border-emerald-800">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-stone-700 mb-1">Judul</label>
                <input type="text" name="judul_laporan" value="{{ old('judul_laporan', $report->judul_laporan) }}" required class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:border-emerald-800">
            </div>

            <div>
                <label class="block text-xs font-medium text-stone-700 mb-1">Isi Laporan</label>
                <textarea name="isi_laporan" rows="5" required class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:border-emerald-800">{{ old('isi_laporan', $report->isi_laporan) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-stone-700 mb-1">Tanggal Kejadian</label>
                    <input type="date" name="tanggal_kejadian" value="{{ old('tanggal_kejadian', $report->tanggal_kejadian?->format('Y-m-d')) }}" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-700 mb-1">Lokasi</label>
                    <input type="text" name="lokasi_kejadian" value="{{ old('lokasi_kejadian', $report->lokasi_kejadian) }}" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm">
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-2 border-t border-stone-100">
                <a href="{{ route('admin.reports.show', $report) }}" class="btn-press px-4 py-2 rounded-lg border border-stone-300 bg-white text-xs font-medium text-stone-700 hover:bg-stone-50">Batal</a>
                <button type="submit" class="btn-press px-5 py-2 rounded-lg bg-emerald-900 text-white text-xs font-semibold hover:bg-emerald-950 cursor-pointer">Simpan</button>
            </div>
        </form>
    </div>
@endsection
