@extends('layouts.admin')

@section('title', 'Kategori Bidang — Admin SILIRA')

@section('content')
    <h1 class="text-2xl font-extrabold text-slate-900">Kategori Bidang Aspirasi</h1>
    <p class="text-sm text-slate-500 mt-0.5">Kelola daftar kategori yang tampil pada formulir pelaporan mahasiswa.</p>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 h-fit">
            <h2 class="text-sm font-bold text-slate-900">Tambah Kategori Baru</h2>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="mt-4 space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kategori</label>
                    <input type="text" name="name" required placeholder="Contoh: Perpustakaan & Lab" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Singkat</label>
                    <input type="text" name="description" placeholder="Keterangan ruang lingkup..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-red-700 text-white text-xs font-bold hover:bg-red-800 cursor-pointer">Tambah Kategori</button>
            </form>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3 text-center">Laporan</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($categories as $cat)
                        <tr>
                            <td class="px-5 py-3.5">
                                <p class="font-semibold text-slate-900">{{ $cat->name }}</p>
                                <p class="text-xs text-slate-500">{{ $cat->description ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-center font-bold text-slate-700 tabular-nums">{{ $cat->reports_count }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $cat->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                <form action="{{ route('admin.categories.update', $cat) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="name" value="{{ $cat->name }}">
                                    <input type="hidden" name="description" value="{{ $cat->description }}">
                                    <input type="hidden" name="is_active" value="{{ $cat->is_active ? '0' : '1' }}">
                                    <button type="submit" class="px-2.5 py-1 rounded-lg border border-slate-300 text-xs font-semibold text-slate-700 hover:bg-slate-100 cursor-pointer">
                                        {{ $cat->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 text-xs font-semibold hover:bg-rose-100 cursor-pointer">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
