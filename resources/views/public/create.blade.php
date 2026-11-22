@extends('layouts.public')

@section('title', 'Buat Laporan — SILIRA DPM FDIK')

@section('content')
    @php
        $categoryOptions = $categories->map(fn ($cat) => [
            'value' => $cat->id,
            'label' => $cat->name,
            'description' => $cat->description,
        ])->values()->all();

        $privacyOptions = [
            [
                'value' => 'public',
                'label' => 'Publik',
                'description' => 'Judul, isi, nama pelapor, dan progres tampil di feed transparansi.',
                'dot' => 'bg-emerald-500',
            ],
            [
                'value' => 'anonymous',
                'label' => 'Anonim',
                'description' => 'Tampil di feed transparansi sebagai "Pelapor Anonim" (nama disembunyikan).',
                'dot' => 'bg-amber-500',
            ],
            [
                'value' => 'confidential',
                'label' => 'Rahasia',
                'description' => 'Tidak tampil di feed publik. Hanya bisa diakses lewat Kode Tiket & Admin DPM.',
                'dot' => 'bg-rose-600',
            ],
        ];
    @endphp

    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
        <div class="max-w-2xl mb-8">
            <span class="text-xs font-bold uppercase tracking-wider text-red-700">Formulir Resmi Tanpa Login</span>
            <h1 class="mt-1.5 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-950">Sampaikan Aspirasi atau Pengaduan</h1>
            <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                Lengkapi informasi di bawah ini dengan jelas. Setelah dikirim, Anda langsung menerima <strong>Kode Tiket</strong> untuk memantau tindak lanjut dari DPM FDIK.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-sm text-rose-900">
                <p class="font-bold">Mohon lengkapi atau perbaiki isian berikut:</p>
                <ul class="mt-2 list-disc list-inside space-y-1 text-xs">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-6 sm:p-8 space-y-7">
                @csrf

                {{-- Step 1: Klasifikasi & Kategori --}}
                <div class="space-y-5">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full bg-red-700 text-white text-xs font-bold flex items-center justify-center">1</span>
                        <h2 class="text-sm font-extrabold text-slate-950">Klasifikasi &amp; Tujuan Laporan</h2>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Jenis Laporan <span class="text-red-600">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            @foreach ([
                                ['Aspirasi', 'Usulan, ide, atau masukan kebijakan'],
                                ['Pengaduan', 'Kendala fasilitas, layanan, atau etika'],
                                ['Permintaan Informasi', 'Pertanyaan jadwal, prosedur, atau data'],
                            ] as [$jenis, $hint])
                                <label class="relative flex flex-col p-3.5 rounded-xl border border-slate-200 bg-slate-50/40 cursor-pointer hover:border-slate-300 has-[:checked]:border-red-600 has-[:checked]:bg-red-50/60 has-[:checked]:ring-2 has-[:checked]:ring-red-600/15 transition">
                                    <input type="radio" name="jenis_laporan" value="{{ $jenis }}" class="sr-only" {{ old('jenis_laporan', 'Aspirasi') === $jenis ? 'checked' : '' }} required>
                                    <span class="text-sm font-bold text-slate-900">{{ $jenis }}</span>
                                    <span class="text-[11px] text-slate-500 mt-0.5 leading-snug">{{ $hint }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Kategori Bidang Tujuan <span class="text-red-600">*</span></label>
                            <x-select-dropdown
                                name="category_id"
                                :options="$categoryOptions"
                                placeholder="Pilih kategori bidang..."
                                :searchable="true"
                                :required="true"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tingkat Privasi <span class="text-red-600">*</span></label>
                            <x-select-dropdown
                                name="privacy"
                                :options="$privacyOptions"
                                selected="public"
                                placeholder="Pilih privasi..."
                                :required="true"
                            />
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- Step 2: Identitas --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full bg-red-700 text-white text-xs font-bold flex items-center justify-center">2</span>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-950">Identitas Pelapor</h2>
                            <p class="text-xs text-slate-500">Hanya dipakai untuk verifikasi civitas FDIK dan kontak tindak lanjut oleh Admin DPM.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="nama_pelapor" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap <span class="text-red-600">*</span></label>
                            <input type="text" id="nama_pelapor" name="nama_pelapor" value="{{ old('nama_pelapor') }}" required placeholder="Contoh: Ahmad Fauzi" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300/90 text-sm focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600 transition">
                        </div>
                        <div>
                            <label for="nim_nip" class="block text-xs font-semibold text-slate-700 mb-1.5">NIM / NIP <span class="text-red-600">*</span></label>
                            <input type="text" id="nim_nip" name="nim_nip" value="{{ old('nim_nip') }}" required placeholder="Contoh: 220401045" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300/90 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600 transition">
                        </div>
                        <div>
                            <label for="jurusan_instansi" class="block text-xs font-semibold text-slate-700 mb-1.5">Jurusan / Prodi / Instansi</label>
                            <input type="text" id="jurusan_instansi" name="jurusan_instansi" value="{{ old('jurusan_instansi') }}" placeholder="Contoh: Komunikasi & Penyiaran Islam (KPI)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300/90 text-sm focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600 transition">
                        </div>
                        <div>
                            <label for="no_wa" class="block text-xs font-semibold text-slate-700 mb-1.5">Nomor WhatsApp Aktif <span class="text-red-600">*</span></label>
                            <input type="tel" id="no_wa" name="no_wa" value="{{ old('no_wa') }}" required placeholder="Contoh: 081234567890" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300/90 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600 transition">
                        </div>
                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- Step 3: Detail Laporan --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full bg-red-700 text-white text-xs font-bold flex items-center justify-center">3</span>
                        <h2 class="text-sm font-extrabold text-slate-950">Uraian &amp; Bukti Pendukung</h2>
                    </div>

                    <div>
                        <label for="judul_laporan" class="block text-xs font-semibold text-slate-700 mb-1.5">Judul Laporan <span class="text-red-600">*</span></label>
                        <input type="text" id="judul_laporan" name="judul_laporan" value="{{ old('judul_laporan') }}" required maxlength="255" placeholder="Ringkas dan spesifik, mis. Proyektor Ruang B.204 Tidak Berfungsi" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300/90 text-sm focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600 transition">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="isi_laporan" class="block text-xs font-semibold text-slate-700">Uraian Lengkap <span class="text-red-600">*</span></label>
                            <span id="charCounter" class="text-xs font-mono text-slate-400 tabular-nums">0 / 3000</span>
                        </div>
                        <textarea id="isi_laporan" name="isi_laporan" rows="6" required maxlength="3000" placeholder="Jelaskan kronologi, lokasi, dampak yang dirasakan, atau usulan solusi secara jelas (minimal 20 karakter)..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300/90 text-sm leading-relaxed focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600 transition">{{ old('isi_laporan') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal_kejadian" class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Kejadian (Opsional)</label>
                            <input type="date" id="tanggal_kejadian" name="tanggal_kejadian" value="{{ old('tanggal_kejadian') }}" max="{{ now()->toDateString() }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300/90 text-sm focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600 transition">
                        </div>
                        <div>
                            <label for="lokasi_kejadian" class="block text-xs font-semibold text-slate-700 mb-1.5">Lokasi Kejadian (Opsional)</label>
                            <input type="text" id="lokasi_kejadian" name="lokasi_kejadian" value="{{ old('lokasi_kejadian') }}" placeholder="Contoh: Gedung B Lantai 2 / Lab TV" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300/90 text-sm focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600 transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Lampiran Bukti (Opsional)</label>
                        <label data-dropzone class="relative flex flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 bg-slate-50/60 px-6 py-6 text-center hover:border-slate-400 hover:bg-slate-50 transition cursor-pointer">
                            <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0 3 3m-3-3-3 3M6.75 19.5a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z"/></svg>
                            <span data-dropzone-filename class="mt-2 text-xs text-slate-500">Klik atau tarik file ke sini (JPG, PNG, WEBP, PDF maks. 5 MB)</span>
                            <input type="file" id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf" class="sr-only">
                        </label>
                    </div>
                </div>

                <div class="pt-5 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-slate-100">
                    <p class="text-xs text-slate-500 leading-relaxed">Dengan mengirim laporan, Anda menyatakan informasi yang disampaikan faktual dan dapat dipertanggungjawabkan.</p>
                    <button type="submit" class="btn-press w-full sm:w-auto px-7 py-3.5 rounded-xl bg-red-700 text-white font-bold text-sm hover:bg-red-800 shadow-sm cursor-pointer whitespace-nowrap">
                        Kirim &amp; Dapatkan Kode Tiket
                    </button>
                </div>
            </form>

            {{-- Helpful Sticky Guide Sidebar --}}
            <aside class="lg:col-span-4 space-y-5 lg:sticky lg:top-24">
                <div class="bg-white rounded-2xl border border-slate-200/90 p-6">
                    <h3 class="text-sm font-extrabold text-slate-950">Panduan Privasi Laporan</h3>
                    <ul class="mt-4 space-y-3.5 text-xs text-slate-600 leading-relaxed">
                        <li class="flex items-start gap-2.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 mt-1.5 shrink-0"></span>
                            <span><strong class="text-slate-900">Publik:</strong> Cocok untuk aspirasi umum agar mahasiswa lain ikut memantau penyelesaiannya.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500 mt-1.5 shrink-0"></span>
                            <span><strong class="text-slate-900">Anonim:</strong> Isi laporan tampil di publik, namun nama &amp; NIM Anda disembunyikan sebagai <em>Pelapor Anonim</em>.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-2 h-2 rounded-full bg-rose-600 mt-1.5 shrink-0"></span>
                            <span><strong class="text-slate-900">Rahasia:</strong> Cocok untuk aduan sensitif. Tidak muncul di publik dan hanya dapat dibuka dengan Kode Tiket.</span>
                        </li>
                    </ul>
                </div>

                <div class="bg-slate-900 text-slate-200 rounded-2xl p-6">
                    <h3 class="text-sm font-bold text-white">Tips Agar Cepat Diproses</h3>
                    <ul class="mt-3 space-y-2 text-xs text-slate-300 leading-relaxed list-disc list-inside">
                        <li>Gunakan judul yang menyebut lokasi atau topik secara spesifik.</li>
                        <li>Sertakan foto bukti atau tangkapan layar bila relevan.</li>
                        <li>Simpan <strong>Kode Tiket</strong> setelah mengirim untuk cek balasan DPM.</li>
                    </ul>
                </div>
            </aside>
        </div>
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
