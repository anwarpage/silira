@extends('layouts.admin')

@section('title', 'Edit ' . $report->ticket_code . ' — Admin SILIRA')

@section('content')
    <div class="max-w-3xl">
        <a href="{{ route('admin.reports.show', $report) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800">&larr; Kembali ke Detail Laporan</a>
        <h1 class="mt-2 text-2xl font-extrabold text-slate-900">Edit Laporan {{ $report->ticket_code }}</h1>

        @if ($errors->any())
            <div class="mt-4 rounded-xl bg-rose-50 border border-rose-200 p-4 text-xs text-rose-800">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.reports.update', $report) }}" method="POST" class="mt-6 bg-white rounded-2xl border border-slate-200 p-6 space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Klasifikasi</label>
                    <select name="jenis_laporan" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                        @foreach (\App\Models\Report::JENIS_OPTIONS as $j)
                            <option value="{{ $j }}" {{ old('jenis_laporan', $report->jenis_laporan) === $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori Bidang</label>
                    <select name="category_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $report->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Privasi</label>
                    <select name="privacy" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                        @foreach (\App\Models\Report::PRIVACY_OPTIONS as $k => $label)
                            <option value="{{ $k }}" {{ old('privacy', $report->privacy) === $k ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                        @foreach (\App\Models\Report::STATUSES as $k => $label)
                            <option value="{{ $k }}" {{ old('status', $report->status) === $k ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pelapor</label>
                    <input type="text" name="nama_pelapor" value="{{ old('nama_pelapor', $report->nama_pelapor) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">NIM / NIP</label>
                    <input type="text" name="nim_nip" value="{{ old('nim_nip', $report->nim_nip) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jurusan / Instansi</label>
                    <input type="text" name="jurusan_instansi" value="{{ old('jurusan_instansi', $report->jurusan_instansi) }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">No. WA</label>
                    <input type="text" name="no_wa" value="{{ old('no_wa', $report->no_wa) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Judul Laporan</label>
                <input type="text" name="judul_laporan" value="{{ old('judul_laporan', $report->judul_laporan) }}" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Isi Laporan</label>
                <textarea name="isi_laporan" rows="5" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">{{ old('isi_laporan', $report->isi_laporan) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Kejadian</label>
                    <input type="date" name="tanggal_kejadian" value="{{ old('tanggal_kejadian', $report->tanggal_kejadian?->format('Y-m-d')) }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Lokasi Kejadian</label>
                    <input type="text" name="lokasi_kejadian" value="{{ old('lokasi_kejadian', $report->lokasi_kejadian) }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('admin.reports.show', $report) }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-100">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-red-700 text-white text-xs font-bold hover:bg-red-800 cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
@endsection
