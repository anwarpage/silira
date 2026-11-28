@extends('layouts.admin')

@section('title', 'Edit ' . $report->ticket_code . ' — SILIRA')

@section('content')
    @php
        $jenisOptions = collect(\App\Models\Report::JENIS_OPTIONS)->map(fn ($j) => ['value' => $j, 'label' => $j])->all();
        $categoryOptions = $categories->map(fn ($c) => ['value' => $c->id, 'label' => $c->name, 'description' => $c->description])->all();
        $privacyOptions = [
            ['value' => 'public', 'label' => 'Publik', 'dot' => 'bg-[#2563eb]'],
            ['value' => 'anonymous', 'label' => 'Anonim', 'dot' => 'bg-[#d97706]'],
            ['value' => 'confidential', 'label' => 'Rahasia', 'dot' => 'bg-[#dc2626]'],
        ];
        $statusDots = [
            'menunggu' => 'bg-[#d97706]',
            'diverifikasi' => 'bg-[#0284c7]',
            'diproses' => 'bg-[#1e3a8a]',
            'selesai' => 'bg-[#16a34a]',
            'ditolak' => 'bg-[#b91c1c]',
        ];
        $statusOptions = collect(\App\Models\Report::STATUSES)->map(fn ($label, $key) => [
            'value' => $key,
            'label' => $label,
            'dot' => $statusDots[$key] ?? null,
        ])->values()->all();
    @endphp

    <div class="max-w-2xl">
        <a href="{{ route('admin.reports.show', $report) }}" class="text-[14px] text-[#475569] hover:text-[#1e3a8a]">&larr; Detail laporan</a>
        <h1 class="mt-2 text-[28px] font-semibold text-[#0f172a]">Edit <span class="font-mono">{{ $report->ticket_code }}</span></h1>

        @if ($errors->any())
            <div class="mt-4 rounded-[9px] bg-[#fee2e2] border border-[#b91c1c]/35 p-4 text-[14px] text-[#991b1b]">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.reports.update', $report) }}" method="POST" class="mt-6 s-card-accent p-6 sm:p-7 space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Klasifikasi</label>
                    <x-select-dropdown name="jenis_laporan" :options="$jenisOptions" :selected="$report->jenis_laporan" />
                </div>
                <div>
                    <label class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Kategori bidang</label>
                    <x-select-dropdown name="category_id" :options="$categoryOptions" :selected="$report->category_id" :searchable="true" />
                </div>
                <div>
                    <label class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Privasi</label>
                    <x-select-dropdown name="privacy" :options="$privacyOptions" :selected="$report->privacy" />
                </div>
                <div>
                    <label class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Status</label>
                    <x-select-dropdown name="status" :options="$statusOptions" :selected="$report->status" />
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Nama pelapor</label>
                    <input type="text" name="nama_pelapor" value="{{ old('nama_pelapor', $report->nama_pelapor) }}" required class="s-input">
                </div>
                <div>
                    <label class="block text-[13px] font-medium text-[#0f172a] mb-1.5">NIM / NIP</label>
                    <input type="text" name="nim_nip" value="{{ old('nim_nip', $report->nim_nip) }}" required class="s-input font-mono">
                </div>
                <div>
                    <label class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Jurusan / instansi</label>
                    <input type="text" name="jurusan_instansi" value="{{ old('jurusan_instansi', $report->jurusan_instansi) }}" class="s-input">
                </div>
                <div>
                    <label class="block text-[13px] font-medium text-[#0f172a] mb-1.5">No. WA</label>
                    <input type="text" name="no_wa" value="{{ old('no_wa', $report->no_wa) }}" required class="s-input font-mono">
                </div>
            </div>

            <div>
                <label class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Judul</label>
                <input type="text" name="judul_laporan" value="{{ old('judul_laporan', $report->judul_laporan) }}" required class="s-input">
            </div>

            <div>
                <label class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Isi laporan</label>
                <textarea name="isi_laporan" rows="5" required class="s-input min-h-[130px] py-3">{{ old('isi_laporan', $report->isi_laporan) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Tanggal kejadian</label>
                    <input type="date" name="tanggal_kejadian" value="{{ old('tanggal_kejadian', $report->tanggal_kejadian?->format('Y-m-d')) }}" class="s-input">
                </div>
                <div>
                    <label class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Lokasi</label>
                    <input type="text" name="lokasi_kejadian" value="{{ old('lokasi_kejadian', $report->lokasi_kejadian) }}" class="s-input">
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-[#e5e7eb]">
                <a href="{{ route('admin.reports.show', $report) }}" class="s-btn-secondary">Batal</a>
                <button type="submit" class="s-btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
@endsection
