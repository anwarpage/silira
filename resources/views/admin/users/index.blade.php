@extends('layouts.admin')

@section('title', 'Akun Pengurus DPM — Admin SILIRA')

@section('content')
    <h1 class="text-2xl font-extrabold text-slate-900">Akun Pengurus DPM</h1>
    <p class="text-sm text-slate-500 mt-0.5">Kelola akses admin DPM FDIK dan perbarui kata sandi akun Anda.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-xl bg-rose-50 border border-rose-200 p-4 text-xs text-rose-800">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Add Admin --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h2 class="text-sm font-bold text-slate-900">Tambah Pengurus Admin</h2>
            <form action="{{ route('admin.users.store') }}" method="POST" class="mt-4 space-y-3">
                @csrf
                <input type="text" name="name" required placeholder="Nama Lengkap" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                <input type="email" name="email" required placeholder="Email (mis. komisi2@silira.ac.id)" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                <input type="text" name="jabatan" placeholder="Jabatan / Komisi (opsional)" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                <x-select-dropdown
                    name="role"
                    :options="[
                        ['value' => 'admin_dpm', 'label' => 'Admin DPM', 'description' => 'Mengelola laporan, tanggapan, dan kategori.'],
                        ['value' => 'superadmin', 'label' => 'Super Admin', 'description' => 'Akses penuh termasuk pengelolaan akun pengurus.'],
                    ]"
                    selected="admin_dpm"
                />
                <div class="grid grid-cols-2 gap-3">
                    <input type="password" name="password" required placeholder="Password (min 8)" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                    <input type="password" name="password_confirmation" required placeholder="Konfirmasi Password" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                </div>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-red-700 text-white text-xs font-bold hover:bg-red-800 cursor-pointer">Tambah Pengurus</button>
            </form>
        </div>

        {{-- Change Own Password --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <h2 class="text-sm font-bold text-slate-900">Ubah Kata Sandi Saya</h2>
            <form action="{{ route('admin.users.password') }}" method="POST" class="mt-4 space-y-3">
                @csrf
                @method('PUT')
                <input type="password" name="current_password" required placeholder="Kata Sandi Saat Ini" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                <input type="password" name="password" required placeholder="Kata Sandi Baru (min 8 karakter)" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                <input type="password" name="password_confirmation" required placeholder="Konfirmasi Kata Sandi Baru" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-sm">
                <button type="submit" class="w-full py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 cursor-pointer">Perbarui Kata Sandi</button>
            </form>
        </div>
    </div>

    {{-- User List --}}
    <div class="mt-6 bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3">Nama &amp; Jabatan</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Peran</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($users as $u)
                    <tr>
                        <td class="px-5 py-3.5">
                            <p class="font-semibold text-slate-900">{{ $u->name }}</p>
                            <p class="text-xs text-slate-500">{{ $u->jabatan ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-slate-700">{{ $u->email }}</td>
                        <td class="px-5 py-3.5 text-xs">
                            <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-semibold">{{ $u->role }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            @if ($u->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" onsubmit="return confirm('Hapus akun pengurus ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 text-xs font-semibold hover:bg-rose-100 cursor-pointer">Hapus</button>
                                </form>
                            @else
                                <span class="text-xs text-slate-400">Akun aktif</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
