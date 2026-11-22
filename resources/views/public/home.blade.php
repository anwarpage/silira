@extends('layouts.public')

@section('title', 'SILIRA — Portal Aspirasi & Pengaduan DPM Fakultas Dakwah dan Ilmu Komunikasi')

@section('content')
    @php
        $featured = $publicReports->first();
        $completionRate = $stats['total'] > 0 ? round(($stats['selesai'] / $stats['total']) * 100) : 0;
    @endphp

    {{-- Hero: Clean Editorial Split + Live Ticket Preview --}}
    <section class="bg-white border-b border-slate-200/80">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-14 lg:py-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                <div class="lg:col-span-7">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 border border-red-200/70 text-red-800 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span>
                        Layanan Aspirasi Resmi DPM FDIK
                    </div>

                    <h1 class="mt-5 text-3xl sm:text-5xl font-extrabold tracking-tight text-slate-950 leading-[1.12]">
                        Saluran Aspirasi Fakultas yang <span class="text-red-700">Terlacak</span>, Bukan Sekadar Masuk Formulir.
                    </h1>

                    <p class="mt-5 text-base sm:text-lg text-slate-600 leading-relaxed max-w-xl">
                        Sampaikan aspirasi, pengaduan, atau permintaan informasi tanpa perlu membuat akun. Dapatkan kode tiket unik untuk memantau tindak lanjut DPM secara terbuka, anonim, atau rahasia.
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('reports.create') }}" class="btn-press inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-red-700 text-white font-bold text-sm hover:bg-red-800 shadow-sm">
                            Tulis Laporan Baru
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                        </a>
                        <a href="{{ route('public.feed') }}" class="btn-press inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl border border-slate-300 bg-white text-slate-800 font-semibold text-sm hover:bg-slate-50 hover:border-slate-400">
                            Jelajahi Aspirasi Publik
                        </a>
                    </div>

                    {{-- Inline Quick-Track Bar --}}
                    <div class="mt-8 pt-7 border-t border-slate-200/80 max-w-xl">
                        <label for="hero_ticket_code" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Sudah punya kode tiket?</label>
                        <form action="{{ route('tracking.lookup') }}" method="POST" class="flex flex-col sm:flex-row gap-2.5">
                            @csrf
                            <input
                                type="text"
                                name="ticket_code"
                                id="hero_ticket_code"
                                placeholder="Masukkan kode tiket, mis. SLR-2026-FDIK01"
                                class="flex-1 px-4 py-3 rounded-xl border border-slate-300 bg-slate-50/70 text-sm font-mono uppercase placeholder:normal-case placeholder:font-sans focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-600/20 focus:border-red-600 transition"
                                required
                            >
                            <button type="submit" class="btn-press px-5 py-3 rounded-xl bg-slate-900 text-white font-semibold text-sm hover:bg-slate-800 whitespace-nowrap cursor-pointer">
                                Lacak Status
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Live Product UI Frame: Real Ticket Card Preview --}}
                <div class="lg:col-span-5">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-2 shadow-sm">
                        <div class="rounded-xl border border-slate-200/90 bg-white p-5 sm:p-6">
                            <div class="flex items-center justify-between gap-2 pb-4 border-b border-slate-100">
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold px-2.5 py-1 rounded-lg bg-slate-900 text-white">
                                        {{ $featured?->ticket_code ?? 'SLR-2026-FDIK01' }}
                                    </span>
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $featured?->status_badge_class ?? 'bg-emerald-50 text-emerald-800 ring-1 ring-inset ring-emerald-600/20' }}">
                                        {{ $featured?->status_label ?? 'Selesai' }}
                                    </span>
                                </div>
                                <span class="text-[11px] font-medium text-slate-400">Live Preview</span>
                            </div>

                            <h2 class="mt-4 text-base font-bold text-slate-950 leading-snug">
                                {{ $featured?->judul_laporan ?? 'Proyektor dan AC Ruang Kuliah B.204 Tidak Berfungsi' }}
                            </h2>
                            <p class="mt-1.5 text-xs text-slate-500">
                                {{ $featured?->category?->name ?? 'Fasilitas & Sarana Prasarana' }} &middot; {{ $featured?->public_reporter_name ?? 'Ahmad Fauzi' }}
                            </p>

                            {{-- Mini Stepper --}}
                            <div class="mt-5 grid grid-cols-4 gap-1.5">
                                @php
                                    $previewOrder = ['menunggu' => 1, 'diverifikasi' => 2, 'diproses' => 3, 'selesai' => 4];
                                    $previewCurrent = $previewOrder[$featured?->status ?? 'selesai'] ?? 4;
                                @endphp
                                @foreach (['Masuk', 'Verifikasi', 'Proses', 'Selesai'] as $idx => $stepLabel)
                                    <div>
                                        <div class="h-1.5 rounded-full {{ ($idx + 1) <= $previewCurrent ? 'bg-red-700' : 'bg-slate-200' }}"></div>
                                        <span class="block mt-1.5 text-[11px] {{ ($idx + 1) <= $previewCurrent ? 'font-semibold text-slate-800' : 'text-slate-400' }}">{{ $stepLabel }}</span>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Simulated / Real Activity Row --}}
                            <div class="mt-5 rounded-xl bg-slate-50 border border-slate-200/70 p-3.5 space-y-2">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-slate-800">Respon Resmi DPM FDIK</span>
                                    <span class="text-slate-400">Terverifikasi</span>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    Setiap perubahan status dan balasan dari pengurus DPM tercatat otomatis di halaman tiket Anda — dapat dibalas kembali tanpa login.
                                </p>
                            </div>

                            @if ($featured)
                                <a href="{{ route('tracking.show', $featured->ticket_code) }}" class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-red-700 hover:text-red-800">
                                    Buka Contoh Halaman Tiket &rarr;
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Transparency Metrics Strip --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 mt-12">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6">
                <p class="text-xs font-semibold text-slate-500">Total Laporan Masuk</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-950 tabular-nums">{{ number_format($stats['total']) }}</p>
                <p class="mt-1 text-xs text-slate-500">Tercatat sepanjang periode</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6">
                <p class="text-xs font-semibold text-amber-700">Menunggu &amp; Verifikasi</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-950 tabular-nums">{{ number_format($stats['menunggu']) }}</p>
                <p class="mt-1 text-xs text-slate-500">Dalam antrean pemeriksaan DPM</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6">
                <p class="text-xs font-semibold text-indigo-700">Sedang Ditindaklanjuti</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-950 tabular-nums">{{ number_format($stats['diproses']) }}</p>
                <p class="mt-1 text-xs text-slate-500">Koordinasi dengan pihak fakultas</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/90 p-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold text-emerald-700">Tuntas / Selesai</p>
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 tabular-nums">{{ $completionRate }}%</span>
                </div>
                <p class="mt-2 text-3xl font-extrabold text-slate-950 tabular-nums">{{ number_format($stats['selesai']) }}</p>
                <p class="mt-1 text-xs text-slate-500">Telah mendapatkan jawaban resmi</p>
            </div>
        </div>
    </section>

    {{-- Why SILIRA, Not Just Google Form? --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 mt-16">
        <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-10">
            <div class="max-w-2xl">
                <span class="text-xs font-bold uppercase tracking-wider text-red-700">Urgensi Sistem</span>
                <h2 class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-950">
                    Mengapa SILIRA, Bukan Sekadar Google Form?
                </h2>
                <p class="mt-2.5 text-sm sm:text-base text-slate-600 leading-relaxed">
                    Google Form cocok untuk mengumpulkan data satu arah, tetapi aspirasi mahasiswa membutuhkan <strong>kepastian status, komunikasi dua arah, perlindungan privasi, dan akuntabilitas publik</strong>.
                </p>
            </div>

            <div class="mt-8 overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 pr-4 font-bold">Kebutuhan Pengelolaan Aspirasi</th>
                            <th class="py-3.5 px-4 font-bold">Google Form Biasa</th>
                            <th class="py-3.5 pl-4 font-bold text-red-700">SILIRA (DPM FDIK)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <tr>
                            <td class="py-4 pr-4 font-semibold text-slate-900">Kepastian Status Laporan</td>
                            <td class="py-4 px-4 text-slate-500">Selesai di tombol <em>Submit</em>; pelapor tidak tahu apakah aspirasinya dibaca atau tenggelam di spreadsheet.</td>
                            <td class="py-4 pl-4 text-slate-800 font-medium">Mendapat <strong>Kode Tiket unik</strong> untuk melacak progres real-time (Menunggu &rarr; Verifikasi &rarr; Diproses &rarr; Selesai).</td>
                        </tr>
                        <tr>
                            <td class="py-4 pr-4 font-semibold text-slate-900">Komunikasi &amp; Bukti Tambahan</td>
                            <td class="py-4 px-4 text-slate-500">Satu arah. Jika DPM butuh klarifikasi, harus mencari kontak manual dan histori terpecah di chat pribadi.</td>
                            <td class="py-4 pl-4 text-slate-800 font-medium"><strong>Interaksi 2 arah tanpa login</strong> langsung di halaman tiket, tersimpan rapi dalam satu timeline resmi.</td>
                        </tr>
                        <tr>
                            <td class="py-4 pr-4 font-semibold text-slate-900">Kontrol Privasi Pelapor</td>
                            <td class="py-4 px-4 text-slate-500">Semua masuk sheet internal tanpa mekanisme transparansi publik yang aman.</td>
                            <td class="py-4 pl-4 text-slate-800 font-medium">3 tingkat privasi terstruktur: <strong>Publik</strong>, <strong>Anonim</strong> (identitas disembunyikan), dan <strong>Rahasia</strong> (tertutup).</td>
                        </tr>
                        <tr>
                            <td class="py-4 pr-4 font-semibold text-slate-900">Akuntabilitas &amp; Estafet DPM</td>
                            <td class="py-4 px-4 text-slate-500">Rekap manual, tidak ada catatan internal terpisah, dan rawan hilang saat pergantian kepengurusan.</td>
                            <td class="py-4 pl-4 text-slate-800 font-medium">Dashboard statistik otomatis, <strong>Catatan Internal DPM</strong> terpisah dari publik, filter &amp; export laporan terarsip.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    {{-- 4-Step Workflow --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 mt-16">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-red-700">Alur Layanan</span>
                <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950">Empat Langkah Hingga Tuntas</h2>
            </div>
            <p class="text-sm text-slate-600 max-w-md">Dirancang sederhana untuk pelapor, namun terstruktur bagi pengurus DPM dalam mengawal tindak lanjut.</p>
        </div>

        <div class="mt-7 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @foreach ([
                ['01', 'Kirim Laporan', 'Pilih klasifikasi, bidang tujuan, dan tingkat privasi. Lampirkan bukti bila ada tanpa perlu login.'],
                ['02', 'Terima Kode Tiket', 'Sistem langsung menerbitkan kode tiket unik (SLR-YYYY-XXXXXX) yang dapat disalin atau dicetak.'],
                ['03', 'Verifikasi & Audiensi', 'Komisi Aspirasi DPM memverifikasi laporan, berkoordinasi dengan fakultas, dan memberi respon resmi.'],
                ['04', 'Pantau & Selesai', 'Pelapor dapat memantau progres, membalas klarifikasi, hingga laporan dinyatakan tuntas.'],
            ] as [$num, $title, $desc])
                <div class="bg-white rounded-2xl border border-slate-200/90 p-6">
                    <span class="font-mono text-xs font-bold px-2.5 py-1 rounded-lg bg-red-50 text-red-700">{{ $num }}</span>
                    <h3 class="mt-4 font-bold text-slate-950 text-base">{{ $title }}</h3>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Public Feed Preview --}}
    <section class="max-w-6xl mx-auto px-4 sm:px-6 mt-16">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-red-700">Transparansi Publik</span>
                <h2 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-950">Aspirasi &amp; Pengaduan Terbaru</h2>
                <p class="mt-1 text-sm text-slate-600">Menampilkan laporan Publik dan Anonim. Laporan Rahasia tidak pernah muncul di halaman ini.</p>
            </div>
            <a href="{{ route('public.feed') }}" class="text-sm font-bold text-red-700 hover:text-red-800 transition">
                Lihat Semua Laporan &rarr;
            </a>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-5">
            @forelse ($publicReports as $item)
                <article class="card-interactive bg-white rounded-2xl border border-slate-200/90 p-6 flex flex-col justify-between hover:border-slate-300 hover:shadow-sm">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="px-2.5 py-0.5 rounded-full font-semibold {{ $item->status_badge_class }}">{{ $item->status_label }}</span>
                            <span class="px-2.5 py-0.5 rounded-full font-medium bg-slate-100 text-slate-700">{{ $item->jenis_laporan }}</span>
                            @if ($item->category)
                                <span class="text-slate-500 font-medium">{{ $item->category->name }}</span>
                            @endif
                        </div>

                        <h3 class="mt-3.5 font-bold text-slate-950 text-base leading-snug">
                            <a href="{{ route('tracking.show', $item->ticket_code) }}" class="hover:text-red-700 transition">
                                {{ $item->judul_laporan }}
                            </a>
                        </h3>
                        <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                            {{ \Illuminate\Support\Str::limit($item->isi_laporan, 165) }}
                        </p>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <div class="flex items-center gap-1.5">
                            <span class="font-semibold text-slate-800">{{ $item->public_reporter_name }}</span>
                            <span>&middot;</span>
                            <span>{{ $item->created_at->diffForHumans() }}</span>
                        </div>
                        <a href="{{ route('tracking.show', $item->ticket_code) }}" class="font-bold text-red-700 hover:underline">
                            {{ $item->public_responses_count }} progres &rarr;
                        </a>
                    </div>
                </article>
            @empty
                <div class="md:col-span-2 bg-white rounded-2xl border border-slate-200 p-10 text-center text-slate-500 text-sm">
                    Belum ada laporan publik yang masuk. Jadilah yang pertama menyampaikan aspirasi Anda.
                </div>
            @endforelse
        </div>
    </section>
@endsection
