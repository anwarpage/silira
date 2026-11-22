@extends('layouts.admin')

@section('title', 'Kategori Bidang — Admin SILIRA')

@section('content')
    <h1 class="text-xl font-bold tracking-[-0.02em] text-stone-950">Kategori Bidang</h1>
    <p class="text-xs text-stone-500 mt-0.5">Kelola pilihan bidang tujuan pada formulir pelaporan.</p>

    <div class="mt-5 grid grid-cols-1 lg:grid-cols-12 gap-5">
        <div class="lg:col-span-4 bg-white rounded-xl border border-stone-200 p-5 h-fit">
            <h2 class="text-xs font-semibold text-stone-950">Tambah Kategori</h2>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="mt-3.5 space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-stone-700 mb-1">Nama Kategori</label>
                    <input type="text" name="name" required placeholder="Contoh: Perpustakaan & Lab" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:border-emerald-800">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-700 mb-1">Deskripsi</label>
                    <input type="text" name="description" placeholder="Keterangan singkat..." class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:border-emerald-800">
                </div>
                <button type="submit" class="btn-press w-full py-2 rounded-lg bg-emerald-900 text-white text-xs font-semibold hover:bg-emerald-950 cursor-pointer">Tambah</button>
            </form>
        </div>

        <div class="lg:col-span-8 bg-white rounded-xl border border-stone-200 overflow-hidden">
            <table class="w-full text-left text-xs">
                <thead class="bg-stone-50 text-[11px] font-medium text-stone-500 border-b border-stone-200">
                    <tr>
                        <th class="px-5 py-2.5">Kategori</th>
                        <th class="px-5 py-2.5 text-center">Laporan</th>
                        <th class="px-5 py-2.5 text-center">Status</th>
                        <th class="px-5 py-2.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach ($categories as $cat)
                        <tr>
                            <td class="px-5 py-3">
                                <p class="font-semibold text-stone-900">{{ $cat->name }}</p>
                                <p class="text-[11px] text-stone-500">{{ $cat->description ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-3 text-center font-mono font-medium text-stone-700">{{ $cat->reports_count }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-md font-medium {{ $cat->is_active ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-stone-100 text-stone-500 border border-stone-200' }}">
                                    {{ $cat->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right whitespace-nowrap">
                                <form action="{{ route('admin.categories.update', $cat) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="name" value="{{ $cat->name }}">
                                    <input type="hidden" name="description" value="{{ $cat->description }}">
                                    <input type="hidden" name="is_active" value="{{ $cat->is_active ? '0' : '1' }}">
                                    <button type="submit" class="px-2.5 py-1 rounded-md border border-stone-300 bg-white text-stone-700 hover:bg-stone-50 cursor-pointer">
                                        {{ $cat->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-md text-rose-700 hover:bg-rose-50 cursor-pointer">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
