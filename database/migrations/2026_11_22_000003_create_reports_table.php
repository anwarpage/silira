<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code', 32)->unique();
            $table->enum('jenis_laporan', ['Aspirasi', 'Pengaduan', 'Permintaan Informasi'])->default('Aspirasi');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->enum('privacy', ['public', 'anonymous', 'confidential'])->default('public');
            $table->string('nama_pelapor', 120);
            $table->string('nim_nip', 60);
            $table->string('jurusan_instansi', 120)->nullable();
            $table->string('no_wa', 25);
            $table->string('judul_laporan', 255);
            $table->text('isi_laporan');
            $table->date('tanggal_kejadian')->nullable();
            $table->string('lokasi_kejadian', 180)->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->enum('status', ['menunggu', 'diverifikasi', 'diproses', 'selesai', 'ditolak'])->default('menunggu');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['privacy', 'created_at']);
            $table->index('jenis_laporan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
