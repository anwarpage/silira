@extends('layouts.admin')

@section('title', 'Akun pengurus — Silira')

@section('content')
    <h1 class="text-[28px] font-medium text-[#0f172a]">Akun pengurus DPM</h1>
    <p class="text-[15px] text-[#475569] mt-1">Kelola akses admin dan perbarui kata sandi akun Anda.</p>

    @if ($errors->any())
        <div class="mt-5 rounded-[8px] bg-[#fee2e2] border border-[#dc2626]/30 p-4 text-[14px] text-[#991b1b]">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="s-card p-6">
            <h2 class="text-[16px] font-medium text-[#0f172a]">Tambah pengurus</h2>
            <form action="{{ route('admin.users.store') }}" method="POST" class="mt-4 space-y-3.5">
                @csrf
                <input type="text" name="name" required placeholder="Nama lengkap" class="s-input">
                <input type="email" name="email" required placeholder="Email (mis. komisi2@silira.ac.id)" class="s-input">
                <input type="text" name="jabatan" placeholder="Jabatan / komisi (opsional)" class="s-input">
                <x-select-dropdown
                    name="role"
                    :options="[
                        ['value' => 'admin_dpm', 'label' => 'Admin DPM', 'description' => 'Mengelola laporan, tanggapan, dan kategori.'],
                        ['value' => 'superadmin', 'label' => 'Super Admin', 'description' => 'Akses penuh termasuk pengelolaan akun pengurus.'],
                    ]"
                    selected="admin_dpm"
                />
                <div class="grid grid-cols-2 gap-3">
                    <input type="password" name="password" required placeholder="Password (min 8)" class="s-input">
                    <input type="password" name="password_confirmation" required placeholder="Konfirmasi" class="s-input">
                </div>
                <button type="submit" class="s-btn-primary w-full">Tambah pengurus</button>
            </form>
        </div>

        <div class="s-card p-6">
            <h2 class="text-[16px] font-medium text-[#0f172a]">Ubah kata sandi saya</h2>
            <form action="{{ route('admin.users.password') }}" method="POST" class="mt-4 space-y-3.5">
                @csrf
                @method('PUT')
                <input type="password" name="current_password" required placeholder="Kata sandi saat ini" class="s-input">
                <input type="password" name="password" required placeholder="Kata sandi baru (min 8 karakter)" class="s-input">
                <input type="password" name="password_confirmation" required placeholder="Konfirmasi kata sandi baru" class="s-input">
                <button type="submit" class="s-btn-secondary w-full h-[44px]">Perbarui kata sandi</button>
            </form>
        </div>
    </div>

    <div class="mt-6 s-card overflow-hidden">
        <table class="w-full text-left text-[14px]">
            <thead class="bg-[#f5f7fa]/60 text-[13px] font-medium text-[#475569] border-b border-[#0f172a]/12">
                <tr>
                    <th class="px-5 py-3">Nama &amp; jabatan</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Peran</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#0f172a]/10">
                @foreach ($users as $u)
                    <tr>
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-[#0f172a]">{{ $u->name }}</p>
                            <p class="text-[13px] text-[#475569]">{{ $u->jabatan ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-3.5 font-mono text-[13px] text-[#0f172a]">{{ $u->email }}</td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-0.5 rounded-[6px] text-[13px] bg-[#eceff4] text-[#0f172a]">{{ $u->role }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if ($u->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" onsubmit="return confirm('Hapus akun pengurus ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1.5 rounded-[6px] text-[13px] font-medium text-[#991b1b] hover:bg-[#fee2e2] cursor-pointer">Hapus</button>
                                </form>
                            @else
                                <span class="text-[13px] text-[#475569]">Aktif</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
