@extends('layouts.public')

@section('title', 'Kirim aspirasi — Silira')

@section('content')
    @php
        $categoryOptions = $categories->map(fn ($cat) => [
            'value' => $cat->id,
            'label' => $cat->name,
            'description' => $cat->description,
        ])->values()->all();

        $privacyOptions = [
            ['value' => 'public', 'label' => 'Publik', 'description' => 'Tampil di daftar aspirasi publik beserta nama pelapor.', 'dot' => 'bg-[#1e3a8a]'],
            ['value' => 'anonymous', 'label' => 'Anonim', 'description' => 'Tampil di daftar publik sebagai Pelapor Anonim.', 'dot' => 'bg-[#d97706]'],
            ['value' => 'confidential', 'label' => 'Rahasia', 'description' => 'Tertutup dari publik. Hanya lewat kode tiket dan Admin DPM.', 'dot' => 'bg-[#dc2626]'],
        ];
    @endphp

    <div class="max-w-[1200px] mx-auto px-6 sm:px-10 py-12">
        <div class="max-w-2xl mb-8">
            <h1 class="text-[32px] sm:text-[38px] font-medium leading-[1.12] text-[#0f172a]">Kirim aspirasi atau pengaduan</h1>
            <p class="mt-2.5 text-[16px] text-[#475569] leading-relaxed">
                Tidak perlu login. Setelah formulir dikirim, Anda langsung menerima kode tiket untuk memantau tindak lanjut DPM FDIK.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-[10px] bg-[#fee2e2] border border-[#dc2626]/30 p-4 text-[14px] text-[#991b1b]">
                <p class="font-medium">Mohon periksa kembali isian berikut:</p>
                <ul class="mt-2 list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" class="lg:col-span-8 s-card p-6 sm:p-8 space-y-7">
                @csrf

                {{-- Klasifikasi --}}
                <div>
                    <label class="block text-[13px] font-medium text-[#475569] mb-2.5">Klasifikasi laporan</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach ([
                            ['Aspirasi', 'Usulan atau masukan kebijakan'],
                            ['Pengaduan', 'Kendala fasilitas atau layanan'],
                            ['Permintaan Informasi', 'Pertanyaan jadwal atau prosedur'],
                        ] as [$jenis, $hint])
                            <label class="flex flex-col p-4 rounded-[8px] border border-[#0f172a]/20 bg-white cursor-pointer hover:border-[#0f172a]/45 has-[:checked]:border-[#1e3a8a] has-[:checked]:bg-[#eff4ff]/45 transition">
                                <input type="radio" name="jenis_laporan" value="{{ $jenis }}" class="sr-only" {{ old('jenis_laporan', 'Aspirasi') === $jenis ? 'checked' : '' }} required>
                                <span class="text-[15px] font-medium text-[#0f172a]">{{ $jenis }}</span>
                                <span class="text-[13px] text-[#475569] mt-1 leading-snug">{{ $hint }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Kategori & Privasi --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[13px] font-medium text-[#475569] mb-2">Kategori bidang tujuan</label>
                        <x-select-dropdown name="category_id" :options="$categoryOptions" placeholder="Pilih kategori bidang..." :searchable="true" :required="true" />
                    </div>
                    <div>
                        <label class="block text-[13px] font-medium text-[#475569] mb-2">Tingkat privasi</label>
                        <x-select-dropdown name="privacy" :options="$privacyOptions" selected="public" :required="true" />
                    </div>
                </div>

                <hr class="border-[#0f172a]/12">

                {{-- Identitas --}}
                <div>
                    <h2 class="text-[17px] font-medium text-[#0f172a]">Identitas pelapor</h2>
                    <p class="mt-1 text-[14px] text-[#475569]">Digunakan untuk verifikasi civitas akademika dan kontak tindak lanjut oleh Admin DPM.</p>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="nama_pelapor" class="block text-[13px] font-medium text-[#475569] mb-1.5">Nama lengkap</label>
                            <input type="text" id="nama_pelapor" name="nama_pelapor" value="{{ old('nama_pelapor') }}" required placeholder="Nama sesuai KTM/NIP" class="s-input">
                        </div>
                        <div>
                            <label for="nim_nip" class="block text-[13px] font-medium text-[#475569] mb-1.5">NIM atau NIP</label>
                            <input type="text" id="nim_nip" name="nim_nip" value="{{ old('nim_nip') }}" required placeholder="220401045" class="s-input font-mono">
                        </div>
                        <div>
                            <label for="jurusan_instansi" class="block text-[13px] font-medium text-[#475569] mb-1.5">Jurusan atau prodi</label>
                            <input type="text" id="jurusan_instansi" name="jurusan_instansi" value="{{ old('jurusan_instansi') }}" placeholder="Contoh: KPI, BPI, MD, PMI" class="s-input">
                        </div>
                        <div>
                            <label for="no_wa" class="block text-[13px] font-medium text-[#475569] mb-1.5">Nomor WhatsApp aktif</label>
                            <input type="tel" id="no_wa" name="no_wa" value="{{ old('no_wa') }}" required placeholder="081234567890" class="s-input font-mono">
                        </div>
                    </div>
                </div>

                <hr class="border-[#0f172a]/12">

                {{-- Detail --}}
                <div class="space-y-4">
                    <h2 class="text-[17px] font-medium text-[#0f172a]">Uraian laporan</h2>

                    <div>
                        <label for="judul_laporan" class="block text-[13px] font-medium text-[#475569] mb-1.5">Judul laporan</label>
                        <input type="text" id="judul_laporan" name="judul_laporan" value="{{ old('judul_laporan') }}" required maxlength="255" placeholder="Ringkas dan spesifik" class="s-input">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="isi_laporan" class="block text-[13px] font-medium text-[#475569]">Uraian lengkap</label>
                            <span id="charCounter" class="text-[13px] font-mono text-[#475569] tabular-nums">0 / 3000</span>
                        </div>
                        <textarea id="isi_laporan" name="isi_laporan" rows="5" required maxlength="3000" placeholder="Jelaskan kronologi, dampak, atau usulan solusi (minimal 20 karakter)..." class="s-input min-h-[140px] py-3 leading-relaxed">{{ old('isi_laporan') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal_kejadian" class="block text-[13px] font-medium text-[#475569] mb-1.5">Tanggal kejadian (opsional)</label>
                            <input type="date" id="tanggal_kejadian" name="tanggal_kejadian" value="{{ old('tanggal_kejadian') }}" max="{{ now()->toDateString() }}" class="s-input">
                        </div>
                        <div>
                            <label for="lokasi_kejadian" class="block text-[13px] font-medium text-[#475569] mb-1.5">Lokasi kejadian (opsional)</label>
                            <input type="text" id="lokasi_kejadian" name="lokasi_kejadian" value="{{ old('lokasi_kejadian') }}" placeholder="Contoh: Ruang B.204" class="s-input">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[13px] font-medium text-[#475569] mb-1.5">Lampiran bukti (opsional)</label>
                        <label data-dropzone class="flex flex-col items-center justify-center rounded-[8px] border border-dashed border-[#0f172a]/30 bg-white px-5 py-6 text-center hover:border-[#1e3a8a] transition cursor-pointer">
                            <span data-dropzone-filename class="text-[14px] text-[#475569]">Klik atau tarik file ke sini (JPG, PNG, WEBP, PDF maks. 5 MB)</span>
                            <input type="file" id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf" class="sr-only">
                        </label>
                    </div>
                </div>

                <div class="pt-5 border-t border-[#0f172a]/12 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <p class="text-[13px] text-[#475569]">Verifikasi awal oleh DPM dilakukan dalam 1–2 hari kerja.</p>
                    <button type="submit" class="s-btn-primary">
                        Kirim aspirasi
                    </button>
                </div>
            </form>

            <aside class="lg:col-span-4 space-y-5 lg:sticky lg:top-24">
                <div class="s-card p-6">
                    <h3 class="text-[16px] font-medium text-[#0f172a]">Pilihan privasi</h3>
                    <dl class="mt-4 space-y-4 text-[14px] text-[#475569] leading-relaxed">
                        <div>
                            <dt class="font-medium text-[#0f172a]">Publik</dt>
                            <dd>Tampil di daftar aspirasi agar mahasiswa lain ikut memantau penyelesaiannya.</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-[#0f172a]">Anonim</dt>
                            <dd>Isi laporan tampil di publik tanpa mencantumkan nama atau NIM Anda.</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-[#0f172a]">Rahasia</dt>
                            <dd>Tidak tampil di publik. Hanya dapat dibuka dengan kode tiket dan Admin DPM.</dd>
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
