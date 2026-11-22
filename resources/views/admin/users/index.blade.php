@extends('layouts.admin')

@section('title', 'Akun Pengurus — Admin SILIRA')

@section('content')
    <h1 class="text-xl font-bold tracking-[-0.02em] text-stone-950">Akun Pengurus DPM</h1>
    <p class="text-xs text-stone-500 mt-0.5">Kelola akses admin dan perbarui kata sandi akun Anda.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg bg-rose-50 border border-rose-200 p-4 text-xs text-rose-900">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-5 grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="bg-white rounded-xl border border-stone-200 p-5">
            <h2 class="text-xs font-semibold text-stone-950">Tambah Pengurus</h2>
            <form action="{{ route('admin.users.store') }}" method="POST" class="mt-3.5 space-y-3">
                @csrf
                <input type="text" name="name" required placeholder="Nama Lengkap" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:border-emerald-800">
                <input type="email" name="email" required placeholder="Email (mis. komisi2@silira.ac.id)" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:border-emerald-800">
                <input type="text" name="jabatan" placeholder="Jabatan / Komisi (opsional)" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:border-emerald-800">
                <x-select-dropdown
                    name="role"
                    :options="[
                        ['value' => 'admin_dpm', 'label' => 'Admin DPM', 'description' => 'Mengelola laporan, tanggapan, dan kategori.'],
                        ['value' => 'superadmin', 'label' => 'Super Admin', 'description' => 'Akses penuh termasuk pengelolaan akun pengurus.'],
                    ]"
                    selected="admin_dpm"
                />
                <div class="grid grid-cols-2 gap-2.5">
                    <input type="password" name="password" required placeholder="Password (min 8)" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:border-emerald-800">
                    <input type="password" name="password_confirmation" required placeholder="Konfirmasi" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:border-emerald-800">
                </div>
                <button type="submit" class="btn-press w-full py-2 rounded-lg bg-emerald-900 text-white text-xs font-semibold hover:bg-emerald-950 cursor-pointer">Tambah Pengurus</button>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-stone-200 p-5">
            <h2 class="text-xs font-semibold text-stone-950">Ubah Kata Sandi Saya</h2>
            <form action="{{ route('admin.users.password') }}" method="POST" class="mt-3.5 space-y-3">
                @csrf
                @method('PUT')
                <input type="password" name="current_password" required placeholder="Kata Sandi Saat Ini" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:border-emerald-800">
                <input type="password" name="password" required placeholder="Kata Sandi Baru (min 8 karakter)" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:border-emerald-800">
                <input type="password" name="password_confirmation" required placeholder="Konfirmasi Kata Sandi Baru" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:border-emerald-800">
                <button type="submit" class="btn-press w-full py-2 rounded-lg bg-stone-900 text-white text-xs font-semibold hover:bg-stone-800 cursor-pointer">Perbarui Kata Sandi</button>
            </form>
        </div>
    </div>

    <div class="mt-5 bg-white rounded-xl border border-stone-200 overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-stone-50 text-[11px] font-medium text-stone-500 border-b border-stone-200">
                <tr>
                    <th class="px-5 py-2.5">Nama &amp; Jabatan</th>
                    <th class="px-5 py-2.5">Email</th>
                    <th class="px-5 py-2.5">Peran</th>
                    <th class="px-5 py-2.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @foreach ($users as $u)
                    <tr>
                        <td class="px-5 py-3">
                            <p class="font-semibold text-stone-900">{{ $u->name }}</p>
                            <p class="text-[11px] text-stone-500">{{ $u->jabatan ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-3 font-mono text-stone-700">{{ $u->email }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-0.5 rounded-md bg-stone-100 text-stone-700 font-medium">{{ $u->role }}</span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            @if ($u->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" onsubmit="return confirm('Hapus akun pengurus ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 rounded-md text-rose-700 hover:bg-rose-50 cursor-pointer">Hapus</button>
                                </form>
                            @else
                                <span class="text-stone-400">Aktif</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
