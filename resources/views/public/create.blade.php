@extends('layouts.public')

@section('title', 'Buat Laporan — SILIRA')

@section('content')
    @php
        $categoryOptions = $categories->map(fn ($cat) => [
            'value' => $cat->id,
            'label' => $cat->name,
            'description' => $cat->description,
        ])->values()->all();

        $privacyOptions = [
            ['value' => 'public', 'label' => 'Publik', 'description' => 'Tampil di daftar aspirasi publik beserta nama pelapor.', 'dot' => 'bg-emerald-600'],
            ['value' => 'anonymous', 'label' => 'Anonim', 'description' => 'Tampil di daftar publik sebagai "Pelapor Anonim".', 'dot' => 'bg-amber-500'],
            ['value' => 'confidential', 'label' => 'Rahasia', 'description' => 'Tertutup dari publik. Hanya lewat kode tiket & Admin DPM.', 'dot' => 'bg-rose-600'],
        ];
    @endphp

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-10">
        <div class="max-w-2xl mb-6">
            <h1 class="text-2xl font-bold tracking-[-0.02em] text-stone-950">Kirim Aspirasi atau Pengaduan</h1>
            <p class="mt-1.5 text-sm text-stone-600">Tidak perlu login. Setelah mengirim, Anda menerima kode tiket untuk memantau tindak lanjut DPM FDIK.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-rose-50 border border-rose-200 p-4 text-xs text-rose-900">
                <p class="font-semibold">Periksa kembali isian berikut:</p>
                <ul class="mt-1.5 list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" class="lg:col-span-8 bg-white rounded-xl border border-stone-200 p-6 sm:p-7 space-y-6">
                @csrf

                {{-- Klasifikasi --}}
                <div>
                    <label class="block text-xs font-semibold text-stone-800 mb-2">Klasifikasi Laporan <span class="text-rose-600">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        @foreach ([
                            ['Aspirasi', 'Usulan atau masukan kebijakan'],
                            ['Pengaduan', 'Kendala fasilitas atau layanan'],
                            ['Permintaan Informasi', 'Pertanyaan jadwal atau prosedur'],
                        ] as [$jenis, $hint])
                            <label class="flex flex-col p-3 rounded-lg border border-stone-200 cursor-pointer hover:border-stone-300 has-[:checked]:border-emerald-800 has-[:checked]:bg-emerald-50/40 transition">
                                <input type="radio" name="jenis_laporan" value="{{ $jenis }}" class="sr-only" {{ old('jenis_laporan', 'Aspirasi') === $jenis ? 'checked' : '' }} required>
                                <span class="text-xs font-semibold text-stone-950">{{ $jenis }}</span>
                                <span class="text-[11px] text-stone-500 mt-0.5">{{ $hint }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Kategori & Privasi --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-stone-800 mb-1.5">Kategori Bidang <span class="text-rose-600">*</span></label>
                        <x-select-dropdown name="category_id" :options="$categoryOptions" placeholder="Pilih kategori bidang..." :searchable="true" :required="true" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-stone-800 mb-1.5">Tingkat Privasi <span class="text-rose-600">*</span></label>
                        <x-select-dropdown name="privacy" :options="$privacyOptions" selected="public" :required="true" />
                    </div>
                </div>

                <hr class="border-stone-100">

                {{-- Identitas --}}
                <div>
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-stone-500 mb-3">Identitas Pelapor (Verifikasi Internal DPM)</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="nama_pelapor" class="block text-xs font-medium text-stone-700 mb-1">Nama Lengkap <span class="text-rose-600">*</span></label>
                            <input type="text" id="nama_pelapor" name="nama_pelapor" value="{{ old('nama_pelapor') }}" required placeholder="Nama sesuai KTM/NIP" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/15 focus:border-emerald-800">
                        </div>
                        <div>
                            <label for="nim_nip" class="block text-xs font-medium text-stone-700 mb-1">NIM / NIP <span class="text-rose-600">*</span></label>
                            <input type="text" id="nim_nip" name="nim_nip" value="{{ old('nim_nip') }}" required placeholder="220401045" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-emerald-900/15 focus:border-emerald-800">
                        </div>
                        <div>
                            <label for="jurusan_instansi" class="block text-xs font-medium text-stone-700 mb-1">Jurusan / Prodi</label>
                            <input type="text" id="jurusan_instansi" name="jurusan_instansi" value="{{ old('jurusan_instansi') }}" placeholder="Contoh: KPI / BPI / MD / PMI" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/15 focus:border-emerald-800">
                        </div>
                        <div>
                            <label for="no_wa" class="block text-xs font-medium text-stone-700 mb-1">No. WhatsApp Aktif <span class="text-rose-600">*</span></label>
                            <input type="tel" id="no_wa" name="no_wa" value="{{ old('no_wa') }}" required placeholder="081234567890" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-emerald-900/15 focus:border-emerald-800">
                        </div>
                    </div>
                </div>

                <hr class="border-stone-100">

                {{-- Detail Laporan --}}
                <div class="space-y-4">
                    <h2 class="text-xs font-semibold uppercase tracking-wider text-stone-500">Isi Laporan</h2>

                    <div>
                        <label for="judul_laporan" class="block text-xs font-medium text-stone-700 mb-1">Judul <span class="text-rose-600">*</span></label>
                        <input type="text" id="judul_laporan" name="judul_laporan" value="{{ old('judul_laporan') }}" required maxlength="255" placeholder="Ringkas dan spesifik" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/15 focus:border-emerald-800">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="isi_laporan" class="block text-xs font-medium text-stone-700">Uraian Lengkap <span class="text-rose-600">*</span></label>
                            <span id="charCounter" class="text-[11px] font-mono text-stone-400 tabular-nums">0 / 3000</span>
                        </div>
                        <textarea id="isi_laporan" name="isi_laporan" rows="5" required maxlength="3000" placeholder="Jelaskan kronologi, dampak, atau usulan solusi (minimal 20 karakter)..." class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm leading-relaxed focus:outline-none focus:ring-2 focus:ring-emerald-900/15 focus:border-emerald-800">{{ old('isi_laporan') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal_kejadian" class="block text-xs font-medium text-stone-700 mb-1">Tanggal Kejadian (Opsional)</label>
                            <input type="date" id="tanggal_kejadian" name="tanggal_kejadian" value="{{ old('tanggal_kejadian') }}" max="{{ now()->toDateString() }}" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/15 focus:border-emerald-800">
                        </div>
                        <div>
                            <label for="lokasi_kejadian" class="block text-xs font-medium text-stone-700 mb-1">Lokasi (Opsional)</label>
                            <input type="text" id="lokasi_kejadian" name="lokasi_kejadian" value="{{ old('lokasi_kejadian') }}" placeholder="Contoh: Ruang B.204" class="w-full px-3 py-2 rounded-lg border border-stone-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-900/15 focus:border-emerald-800">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-stone-700 mb-1">Lampiran Bukti (Opsional)</label>
                        <label data-dropzone class="flex flex-col items-center justify-center rounded-lg border border-dashed border-stone-300 bg-stone-50/60 px-4 py-5 text-center hover:border-stone-400 transition cursor-pointer">
                            <span data-dropzone-filename class="text-xs text-stone-500">Klik atau tarik file ke sini (JPG, PNG, WEBP, PDF maks. 5 MB)</span>
                            <input type="file" id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf" class="sr-only">
                        </label>
                    </div>
                </div>

                <div class="pt-4 border-t border-stone-100 flex items-center justify-between gap-4">
                    <p class="text-[11px] text-stone-500">Kode tiket langsung diterbitkan setelah laporan terkirim.</p>
                    <button type="submit" class="btn-press px-5 py-2.5 rounded-lg bg-emerald-900 text-white text-sm font-semibold hover:bg-emerald-950 cursor-pointer whitespace-nowrap">
                        Kirim Laporan
                    </button>
                </div>
            </form>

            <aside class="lg:col-span-4 space-y-4 lg:sticky lg:top-20">
                <div class="bg-white rounded-xl border border-stone-200 p-5">
                    <h3 class="text-xs font-semibold text-stone-950">Tentang Privasi</h3>
                    <dl class="mt-3 space-y-2.5 text-xs text-stone-600 leading-relaxed">
                        <div>
                            <dt class="font-semibold text-stone-900">Publik</dt>
                            <dd>Tampil di feed aspirasi agar civitas ikut memantau tindak lanjut.</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-stone-900">Anonim</dt>
                            <dd>Isi laporan tampil di publik tanpa mencantumkan nama atau NIM Anda.</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-stone-900">Rahasia</dt>
                            <dd>Tidak tampil di publik. Hanya bisa dibuka dengan kode tiket &amp; Admin DPM.</dd>
                        </div>
                    </dl>
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
