@extends('layouts.public')

@section('title', 'Buat Laporan — SILIRA DPM FDIK')

@section('content')
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-10">
        <div class="mb-6">
            <span class="text-xs font-bold uppercase tracking-wider text-red-700">Formulir Aspirasi &amp; Pengaduan</span>
            <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold text-slate-900">Sampaikan Laporan Anda</h1>
            <p class="mt-2 text-sm text-slate-600">
                Setelah mengirim formulir, Anda akan mendapatkan <strong>Kode Tiket unik</strong> untuk memantau status dan berinteraksi langsung dengan Admin DPM tanpa perlu login.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-800">
                <p class="font-bold">Mohon periksa kembali isian berikut:</p>
                <ul class="mt-2 list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">
            @csrf

            {{-- Jenis Laporan --}}
            <div>
                <label class="block text-sm font-bold text-slate-800 mb-2">1. Pilih Klasifikasi Laporan <span class="text-red-600">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach (\App\Models\Report::JENIS_OPTIONS as $jenis)
                        <label class="relative flex items-center justify-center p-3.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 has-[:checked]:border-red-600 has-[:checked]:bg-red-50 has-[:checked]:text-red-800 transition">
                            <input type="radio" name="jenis_laporan" value="{{ $jenis }}" class="sr-only" {{ old('jenis_laporan', 'Aspirasi') === $jenis ? 'checked' : '' }} required>
                            <span class="text-sm font-semibold">{{ $jenis }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Kategori & Privasi --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="category_id" class="block text-sm font-bold text-slate-800 mb-1.5">Kategori / Bidang Tujuan <span class="text-red-600">*</span></label>
                    <select name="category_id" id="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="privacy" class="block text-sm font-bold text-slate-800 mb-1.5">Opsi Privasi <span class="text-red-600">*</span></label>
                    <select name="privacy" id="privacy" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600">
                        <option value="public" {{ old('privacy', 'public') === 'public' ? 'selected' : '' }}>Publik — Tampil di feed transparansi</option>
                        <option value="anonymous" {{ old('privacy') === 'anonymous' ? 'selected' : '' }}>Anonim — Tampil di feed tanpa nama Anda</option>
                        <option value="confidential" {{ old('privacy') === 'confidential' ? 'selected' : '' }}>Rahasia — Hanya via kode tiket &amp; Admin DPM</option>
                    </select>
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- Identitas Pelapor --}}
            <div>
                <h2 class="text-sm font-bold text-slate-800">2. Identitas Pelapor</h2>
                <p class="text-xs text-slate-500 mt-0.5">Digunakan untuk verifikasi civitas akademika dan kontak tindak lanjut oleh DPM (tetap terjaga jika memilih Anonim/Rahasia).</p>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="nama_pelapor" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-red-600">*</span></label>
                        <input type="text" id="nama_pelapor" name="nama_pelapor" value="{{ old('nama_pelapor') }}" required placeholder="Contoh: Ahmad Fauzi" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600">
                    </div>
                    <div>
                        <label for="nim_nip" class="block text-xs font-semibold text-slate-700 mb-1">NIM / NIP <span class="text-red-600">*</span></label>
                        <input type="text" id="nim_nip" name="nim_nip" value="{{ old('nim_nip') }}" required placeholder="Contoh: 220401045" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600">
                    </div>
                    <div>
                        <label for="jurusan_instansi" class="block text-xs font-semibold text-slate-700 mb-1">Jurusan / Prodi / Instansi</label>
                        <input type="text" id="jurusan_instansi" name="jurusan_instansi" value="{{ old('jurusan_instansi') }}" placeholder="Contoh: Komunikasi & Penyiaran Islam (KPI)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600">
                    </div>
                    <div>
                        <label for="no_wa" class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp Aktif <span class="text-red-600">*</span></label>
                        <input type="tel" id="no_wa" name="no_wa" value="{{ old('no_wa') }}" required placeholder="Contoh: 081234567890" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600">
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- Detail Laporan --}}
            <div class="space-y-4">
                <h2 class="text-sm font-bold text-slate-800">3. Detail Aspirasi / Pengaduan</h2>

                <div>
                    <label for="judul_laporan" class="block text-xs font-semibold text-slate-700 mb-1">Judul Laporan <span class="text-red-600">*</span></label>
                    <input type="text" id="judul_laporan" name="judul_laporan" value="{{ old('judul_laporan') }}" required maxlength="255" placeholder="Ringkas dan jelas, contoh: Proyektor Ruang B.204 Tidak Berfungsi" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="isi_laporan" class="block text-xs font-semibold text-slate-700">Uraian Lengkap <span class="text-red-600">*</span></label>
                        <span id="charCounter" class="text-xs text-slate-400 tabular-nums">0 / 3000</span>
                    </div>
                    <textarea id="isi_laporan" name="isi_laporan" rows="6" required maxlength="3000" placeholder="Jelaskan kronologi, dampak, atau usulan solusi secara jelas (minimal 20 karakter)..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600">{{ old('isi_laporan') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal_kejadian" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Kejadian (Opsional)</label>
                        <input type="date" id="tanggal_kejadian" name="tanggal_kejadian" value="{{ old('tanggal_kejadian') }}" max="{{ now()->toDateString() }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600">
                    </div>
                    <div>
                        <label for="lokasi_kejadian" class="block text-xs font-semibold text-slate-700 mb-1">Lokasi Kejadian (Opsional)</label>
                        <input type="text" id="lokasi_kejadian" name="lokasi_kejadian" value="{{ old('lokasi_kejadian') }}" placeholder="Contoh: Gedung B Lantai 2 / Lab TV" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600">
                    </div>
                </div>

                <div>
                    <label for="attachment" class="block text-xs font-semibold text-slate-700 mb-1">Lampiran Bukti (Opsional — JPG, PNG, WEBP, PDF maks. 5 MB)</label>
                    <input type="file" id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf" class="w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer">
                </div>
            </div>

            <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-100">
                <p class="text-xs text-slate-500">Dengan mengirim laporan, Anda menyatakan informasi yang disampaikan dapat dipertanggungjawabkan.</p>
                <button type="submit" class="w-full sm:w-auto px-7 py-3 rounded-xl bg-red-700 text-white font-bold text-sm hover:bg-red-800 transition shadow-xs cursor-pointer whitespace-nowrap">
                    Kirim Laporan
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const textarea = document.getElementById('isi_laporan');
            const counter = document.getElementById('charCounter');
            const update = () => { counter.textContent = `${textarea.value.length} / 3000`; };
            textarea.addEventListener('input', update);
            update();
        });
    </script>
@endsection
