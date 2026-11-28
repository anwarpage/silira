@extends('layouts.public')

@section('title', 'Kirim Aspirasi — SILIRA')

@section('content')
    @php
        $categoryOptions = $categories->map(fn ($cat) => [
            'value' => $cat->id,
            'label' => $cat->name,
            'description' => $cat->description,
        ])->values()->all();

        $privacyOptions = [
            ['value' => 'public', 'label' => 'Publik', 'description' => 'Tampil di direktori aspirasi publik beserta nama pelapor.', 'dot' => 'bg-[#2563eb]'],
            ['value' => 'anonymous', 'label' => 'Anonim', 'description' => 'Tampil di direktori publik sebagai Pelapor Anonim.', 'dot' => 'bg-[#d97706]'],
            ['value' => 'confidential', 'label' => 'Rahasia', 'description' => 'Tertutup dari publik. Hanya lewat nomor tiket, PIN, dan Admin DPM.', 'dot' => 'bg-[#dc2626]'],
        ];
    @endphp

    {{-- Institutional Page Header Band --}}
    <div class="bg-white border-b border-[#e5e7eb]">
        <div class="max-w-[1180px] mx-auto px-5 sm:px-8 py-9">
            <nav aria-label="Breadcrumb" class="text-[13px] text-[#475569] mb-3">
                <a href="{{ route('home') }}" class="hover:text-[#1e3a8a] transition">Beranda</a>
                <span class="mx-2 text-[#d0d7de]" aria-hidden="true">/</span>
                <span class="text-[#0f172a] font-medium">Formulir Aspirasi</span>
            </nav>

            <div class="max-w-2xl">
                <h1 class="text-[30px] sm:text-[36px] font-semibold leading-tight text-[#0f172a]">
                    Formulir Penyampaian Aspirasi &amp; Pengaduan
                </h1>
                <p class="mt-2 text-[15.5px] text-[#475569] leading-relaxed">
                    Layanan terbuka tanpa login. Setelah berkas dikirim, sistem menerbitkan nomor tiket, PIN akses pribadi, dan tanda terima PDF resmi.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-[1180px] mx-auto px-5 sm:px-8 py-9">
        @if ($errors->any())
            <div class="mb-6 rounded-[9px] bg-[#fee2e2] border border-[#b91c1c]/35 p-4 text-[14px] text-[#991b1b]">
                <p class="font-semibold">Mohon periksa kembali isian berikut:</p>
                <ul class="mt-2 list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <form action="{{ route('reports.store') }}" method="POST" enctype="multipart/form-data" class="lg:col-span-8 s-card-accent p-6 sm:p-8 space-y-7">
                @csrf

                {{-- Klasifikasi --}}
                <div>
                    <label class="block text-[13.5px] font-semibold text-[#0f172a] mb-2.5">1. Klasifikasi laporan</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach ([
                            ['Aspirasi', 'Usulan atau masukan kebijakan'],
                            ['Pengaduan', 'Kendala fasilitas atau layanan'],
                            ['Permintaan Informasi', 'Pertanyaan jadwal atau prosedur'],
                        ] as [$jenis, $hint])
                            <label class="flex flex-col p-4 rounded-[9px] border border-[#d0d7de] bg-white cursor-pointer hover:border-[#0f172a] has-[:checked]:border-[#1e3a8a] has-[:checked]:bg-[#eff6ff]/70 transition">
                                <input type="radio" name="jenis_laporan" value="{{ $jenis }}" class="sr-only" {{ old('jenis_laporan', 'Aspirasi') === $jenis ? 'checked' : '' }} required>
                                <span class="text-[15px] font-semibold text-[#0f172a]">{{ $jenis }}</span>
                                <span class="text-[13px] text-[#475569] mt-1 leading-snug">{{ $hint }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Kategori & Privasi --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[13.5px] font-semibold text-[#0f172a] mb-2">Kategori bidang tujuan</label>
                        <x-select-dropdown name="category_id" :options="$categoryOptions" placeholder="Pilih kategori bidang..." :searchable="true" :required="true" />
                    </div>
                    <div>
                        <label class="block text-[13.5px] font-semibold text-[#0f172a] mb-2">Tingkat privasi</label>
                        <x-select-dropdown name="privacy" :options="$privacyOptions" selected="public" :required="true" />
                    </div>
                </div>

                <hr class="border-[#e5e7eb]">

                {{-- Identitas --}}
                <div>
                    <h2 class="font-sans text-[16.5px] font-semibold text-[#0f172a]">2. Identitas pelapor</h2>
                    <p class="mt-1 text-[14px] text-[#475569]">Digunakan untuk verifikasi civitas akademika dan kontak tindak lanjut resmi oleh Admin DPM.</p>

                    <div id="privacyNotice" class="hidden mt-3 rounded-[10px] border px-3.5 py-2.5 text-[13.5px] leading-relaxed"></div>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="nama_pelapor" class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Nama lengkap</label>
                            <input type="text" id="nama_pelapor" name="nama_pelapor" value="{{ old('nama_pelapor') }}" required placeholder="Nama sesuai KTM/NIP" class="s-input">
                        </div>
                        <div>
                            <label for="nim_nip" class="block text-[13px] font-medium text-[#0f172a] mb-1.5">NIM atau NIP</label>
                            <input type="text" id="nim_nip" name="nim_nip" value="{{ old('nim_nip') }}" required placeholder="220401045" class="s-input font-mono">
                        </div>
                        <div>
                            <label for="jurusan_instansi" class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Jurusan atau prodi</label>
                            <input type="text" id="jurusan_instansi" name="jurusan_instansi" value="{{ old('jurusan_instansi') }}" placeholder="Contoh: KPI, BPI, MD, PMI" class="s-input">
                        </div>
                        <div>
                            <label for="no_wa" class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Nomor WhatsApp aktif</label>
                            <input type="tel" id="no_wa" name="no_wa" value="{{ old('no_wa') }}" required placeholder="081234567890" class="s-input font-mono">
                        </div>
                    </div>
                </div>

                <hr class="border-[#e5e7eb]">

                {{-- Detail --}}
                <div class="space-y-4">
                    <h2 class="font-sans text-[16.5px] font-semibold text-[#0f172a]">3. Uraian laporan</h2>

                    <div>
                        <label for="judul_laporan" class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Judul laporan</label>
                        <input type="text" id="judul_laporan" name="judul_laporan" value="{{ old('judul_laporan') }}" required maxlength="255" placeholder="Ringkas dan spesifik" class="s-input">
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="isi_laporan" class="block text-[13px] font-medium text-[#0f172a]">Uraian lengkap</label>
                            <span id="charCounter" class="text-[13px] font-mono text-[#475569] tabular-nums">0 / 3000</span>
                        </div>
                        <textarea id="isi_laporan" name="isi_laporan" rows="5" required maxlength="3000" placeholder="Jelaskan kronologi, dampak, atau usulan solusi (minimal 20 karakter)..." class="s-input min-h-[140px] py-3 leading-relaxed">{{ old('isi_laporan') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal_kejadian" class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Tanggal kejadian (opsional)</label>
                            <input type="date" id="tanggal_kejadian" name="tanggal_kejadian" value="{{ old('tanggal_kejadian') }}" max="{{ now()->toDateString() }}" class="s-input">
                        </div>
                        <div>
                            <label for="lokasi_kejadian" class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Lokasi kejadian (opsional)</label>
                            <input type="text" id="lokasi_kejadian" name="lokasi_kejadian" value="{{ old('lokasi_kejadian') }}" placeholder="Contoh: Ruang B.204" class="s-input">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[13px] font-medium text-[#0f172a] mb-1.5">Lampiran bukti pendukung (opsional)</label>
                        <label data-dropzone class="flex flex-col items-center justify-center rounded-[9px] border border-dashed border-[#d0d7de] bg-[#f8fafc] px-5 py-6 text-center hover:border-[#1e3a8a] transition cursor-pointer">
                            <span data-dropzone-filename class="text-[14px] text-[#475569]">Klik atau tarik berkas ke sini (JPG, PNG, WEBP, PDF maks. 5 MB)</span>
                            <input type="file" id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.webp,.pdf" class="sr-only">
                        </label>
                    </div>
                </div>

                <div class="pt-5 border-t border-[#e5e7eb] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <p class="text-[13.5px] text-[#475569]">Verifikasi awal oleh Komisi Aspirasi DPM dilakukan dalam 1&ndash;2 hari kerja.</p>
                    <button type="submit" data-loading-text="Mengirim laporan..." class="s-btn-primary shrink-0">
                        Kirim Aspirasi
                    </button>
                </div>
            </form>

            <aside class="lg:col-span-4 space-y-5 lg:sticky lg:top-24">
                <div class="s-card p-6">
                    <h3 class="text-[15.5px] font-semibold text-[#0f172a] pb-3 border-b border-[#e5e7eb]">Panduan tingkat privasi</h3>
                    <dl class="mt-4 space-y-4 text-[14px] text-[#475569] leading-relaxed">
                        <div>
                            <dt class="font-semibold text-[#0f172a] flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#2563eb]" aria-hidden="true"></span>
                                Publik
                            </dt>
                            <dd class="mt-0.5">Tampil di direktori aspirasi agar civitas lain dapat ikut memantau penyelesaiannya.</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-[#0f172a] flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#d97706]" aria-hidden="true"></span>
                                Anonim
                            </dt>
                            <dd class="mt-0.5">Isi laporan tampil di direktori publik tanpa mencantumkan nama atau NIM Anda.</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-[#0f172a] flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#dc2626]" aria-hidden="true"></span>
                                Rahasia
                            </dt>
                            <dd class="mt-0.5">Tidak tampil di publik. Hanya dapat dibuka dengan nomor tiket, PIN akses, dan Admin DPM.</dd>
                        </div>
                    </dl>
                </div>

                <div class="s-card p-5 bg-[#fbf6ea] border-[#b5852a]/40">
                    <p class="text-[13.5px] font-semibold text-[#0f172a]">Simpan nomor tiket &amp; PIN Anda</p>
                    <p class="mt-1 text-[13.5px] text-[#475569] leading-relaxed">
                        PIN akses pribadi hanya ditampilkan satu kali pada halaman tanda terima. Unduh berkas PDF setelah melapor agar tidak hilang.
                    </p>
                </div>
            </aside>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const textarea = document.getElementById('isi_laporan');
            const counter = document.getElementById('charCounter');
            const updateCounter = () => {
                const len = textarea.value.length;
                if (len > 0 && len < 20) {
                    counter.textContent = `${len} / 3000 (min. 20)`;
                    counter.classList.add('text-[#b45309]');
                    counter.classList.remove('text-[#475569]');
                } else {
                    counter.textContent = `${len} / 3000`;
                    counter.classList.remove('text-[#b45309]');
                    counter.classList.add('text-[#475569]');
                }
            };
            textarea.addEventListener('input', updateCounter);
            updateCounter();

            const privacyInput = document.querySelector('input[name="privacy"]');
            const privacyNotice = document.getElementById('privacyNotice');
            const updatePrivacyNotice = () => {
                if (!privacyInput || !privacyNotice) return;
                const val = privacyInput.value;
                if (val === 'anonymous') {
                    privacyNotice.textContent = 'Mode Anonim aktif — Nama, NIM/NIP, dan WhatsApp Anda tidak ditampilkan di direktori publik dan hanya digunakan Admin DPM untuk verifikasi.';
                    privacyNotice.className = 'mt-3 rounded-[10px] border border-[#d97706]/35 bg-[#fffbeb] px-3.5 py-2.5 text-[13.5px] text-[#92400e] leading-relaxed';
                } else if (val === 'confidential') {
                    privacyNotice.textContent = 'Mode Rahasia aktif — Seluruh isi laporan dan identitas Anda tertutup dari publik. Hanya Anda (via PIN akses) dan Admin DPM yang dapat membukanya.';
                    privacyNotice.className = 'mt-3 rounded-[10px] border border-[#dc2626]/30 bg-[#fef2f2] px-3.5 py-2.5 text-[13.5px] text-[#991b1b] leading-relaxed';
                } else {
                    privacyNotice.className = 'hidden mt-3 rounded-[10px] border px-3.5 py-2.5 text-[13.5px] leading-relaxed';
                    privacyNotice.textContent = '';
                }
            };
            privacyInput?.addEventListener('change', updatePrivacyNotice);
            updatePrivacyNotice();
        });
    </script>
@endsection
