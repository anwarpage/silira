@extends('layouts.admin')

@section('title', 'Kategori bidang — Silira')

@section('content')
    <h1 class="text-[28px] font-medium text-[#2a1d17]">Kategori bidang</h1>
    <p class="text-[15px] text-[#65564c] mt-1">Kelola pilihan bidang tujuan pada formulir pelaporan.</p>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-4 s-card p-6 h-fit">
            <h2 class="text-[16px] font-medium text-[#2a1d17]">Tambah kategori</h2>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label class="block text-[13px] font-medium text-[#65564c] mb-1.5">Nama kategori</label>
                    <input type="text" name="name" required placeholder="Contoh: Perpustakaan & Lab" class="s-input">
                </div>
                <div>
                    <label class="block text-[13px] font-medium text-[#65564c] mb-1.5">Deskripsi singkat</label>
                    <input type="text" name="description" placeholder="Keterangan ruang lingkup..." class="s-input">
                </div>
                <button type="submit" class="s-btn-primary w-full">Tambah kategori</button>
            </form>
        </div>

        <div class="lg:col-span-8 s-card overflow-hidden">
            <table class="w-full text-left text-[14px]">
                <thead class="bg-[#e5e1d8]/60 text-[13px] font-medium text-[#65564c] border-b border-[#2a1d17]/12">
                    <tr>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3 text-center">Laporan</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#2a1d17]/10">
                    @foreach ($categories as $cat)
                        <tr>
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-[#2a1d17]">{{ $cat->name }}</p>
                                <p class="text-[13px] text-[#65564c]">{{ $cat->description ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-center font-mono text-[#2a1d17]">{{ $cat->reports_count }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-[6px] text-[13px] font-medium {{ $cat->is_active ? 'bg-[#d9e7d6] text-[#284b20]' : 'bg-[#dad5ca] text-[#65564c]' }}">
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
                                    <button type="submit" class="s-btn-secondary h-[34px] px-3 text-[13px]">
                                        {{ $cat->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 rounded-[6px] text-[13px] font-medium text-[#7a2824] hover:bg-[#eedcdb] cursor-pointer">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
