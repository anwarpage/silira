<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'ticket_code',
    'jenis_laporan',
    'category_id',
    'privacy',
    'nama_pelapor',
    'nim_nip',
    'jurusan_instansi',
    'no_wa',
    'judul_laporan',
    'isi_laporan',
    'tanggal_kejadian',
    'lokasi_kejadian',
    'attachment_path',
    'attachment_name',
    'status',
    'verified_at',
    'resolved_at',
])]
class Report extends Model
{
    public const STATUSES = [
        'menunggu' => 'Menunggu Verifikasi',
        'diverifikasi' => 'Diverifikasi',
        'diproses' => 'Sedang Diproses',
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak',
    ];

    public const JENIS_OPTIONS = [
        'Aspirasi',
        'Pengaduan',
        'Permintaan Informasi',
    ];

    public const PRIVACY_OPTIONS = [
        'public' => 'Publik',
        'anonymous' => 'Anonim',
        'confidential' => 'Rahasia',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kejadian' => 'date',
            'verified_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(ReportResponse::class)->orderBy('created_at');
    }

    public function publicResponses(): HasMany
    {
        return $this->hasMany(ReportResponse::class)
            ->where('is_internal', false)
            ->orderBy('created_at');
    }

    public function scopeVisiblePublicly(Builder $query): Builder
    {
        return $query->whereIn('privacy', ['public', 'anonymous']);
    }

    public static function generateTicketCode(): string
    {
        $year = now()->format('Y');

        do {
            $random = strtoupper(Str::random(6));
            $code = "SLR-{$year}-{$random}";
        } while (static::where('ticket_code', $code)->exists());

        return $code;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getPrivacyLabelAttribute(): string
    {
        return self::PRIVACY_OPTIONS[$this->privacy] ?? ucfirst($this->privacy);
    }

    public function getPublicReporterNameAttribute(): string
    {
        return $this->privacy === 'anonymous' ? 'Pelapor Anonim' : $this->nama_pelapor;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'bg-amber-50 text-amber-800 border border-amber-200/80',
            'diverifikasi' => 'bg-sky-50 text-sky-800 border border-sky-200/80',
            'diproses' => 'bg-indigo-50 text-indigo-800 border border-indigo-200/80',
            'selesai' => 'bg-emerald-50 text-emerald-800 border border-emerald-200/80',
            'ditolak' => 'bg-rose-50 text-rose-800 border border-rose-200/80',
            default => 'bg-stone-100 text-stone-700 border border-stone-200',
        };
    }

    public function getWhatsappUrlAttribute(): string
    {
        $digits = preg_replace('/\D+/', '', $this->no_wa ?? '');
        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif ($digits !== '' && ! str_starts_with($digits, '62')) {
            $digits = '62'.$digits;
        }

        $text = rawurlencode("Halo {$this->nama_pelapor}, kami dari DPM Fakultas Dakwah dan Ilmu Komunikasi menindaklanjuti laporan SILIRA Anda dengan kode tiket {$this->ticket_code} ({$this->judul_laporan}).");

        return "https://wa.me/{$digits}?text={$text}";
    }

    public function canReporterReply(): bool
    {
        return ! in_array($this->status, ['selesai', 'ditolak'], true);
    }

    public function isAttachmentImage(): bool
    {
        if (! $this->attachment_path) {
            return false;
        }

        $ext = strtolower(pathinfo($this->attachment_path, PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);
    }
}
