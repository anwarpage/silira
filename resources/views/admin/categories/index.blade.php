@extends('layouts.admin')

@section('title', 'Kategori Bidang — SILIRA')

@section('content')
    <div class="pb-6 border-b border-[#e5e7eb]">
        <h1 class="text-[30px] font-semibold text-[#0f172a]">Kategori Bidang</h1>
        <p class="text-[15px] text-[#475569] mt-1">Kelola pilihan bidang tujuan pada formulir pelaporan civitas FDIK.</p>
    </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-4 s-card-accent p-6 h-fit">
            <h2 class="font-sans text-[16px] font-semibold text-[#0f172a]">Tambah kategori</h2>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="category_name" class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Nama kategori</label>
                    <input type="text" id="category_name" name="name" value="{{ old('name') }}" required placeholder="Contoh: Perpustakaan & Lab" class="s-input">
                    @error('name')
                        <p class="mt-1.5 text-[13px] text-[#991b1b]">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="category_description" class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Deskripsi singkat</label>
                    <input type="text" id="category_description" name="description" value="{{ old('description') }}" placeholder="Keterangan ruang lingkup..." class="s-input">
                    @error('description')
                        <p class="mt-1.5 text-[13px] text-[#991b1b]">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="s-btn-primary w-full">Tambah Kategori</button>
            </form>
        </div>

        <div class="lg:col-span-8 s-card overflow-hidden">
            <table class="w-full text-left text-[14px]">
                <thead class="bg-[#f8fafc] text-[12.5px] font-medium text-[#475569] border-b border-[#e5e7eb]">
                    <tr>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3 text-center">Laporan</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#e5e7eb]">
                    @foreach ($categories as $cat)
                        <tr class="hover:bg-[#f8fafc] transition">
                            <td class="px-5 py-3.5">
                                <p class="font-medium text-[#0f172a]">{{ $cat->name }}</p>
                                <p class="text-[13px] text-[#475569]">{{ $cat->description ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-center font-mono text-[#0f172a]">{{ $cat->reports_count }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2.5 py-0.5 rounded-[6px] text-[12.5px] font-medium {{ $cat->is_active ? 'bg-[#eff6ff] text-[#1e3a8a] border border-[#1e3a8a]/25' : 'bg-[#f8fafc] text-[#475569] border border-[#e5e7eb]' }}">
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
                                <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" data-confirm-title="Hapus kategori {{ $cat->name }}?" data-confirm-message="Kategori ini akan dihapus dari daftar pilihan formulir." class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 rounded-[9px] text-[13px] font-medium text-[#991b1b] hover:bg-[#fee2e2] cursor-pointer">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
