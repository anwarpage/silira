<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

#[Fillable([
    'ticket_code',
    'access_pin',
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
#[Hidden(['access_pin'])]
class Report extends Model
{
    private const CODE_ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

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
            $random = self::randomCode(8);
            $code = "SLR-{$year}-{$random}";
        } while (static::where('ticket_code', $code)->exists());

        return $code;
    }

    public static function generateAccessPin(): string
    {
        return self::randomCode(6);
    }

    private static function randomCode(int $length): string
    {
        $alphabet = self::CODE_ALPHABET;
        $maxIndex = strlen($alphabet) - 1;
        $result = '';

        for ($i = 0; $i < $length; $i++) {
            $result .= $alphabet[random_int(0, $maxIndex)];
        }

        return $result;
    }

    public function verifyAccessPin(?string $pin): bool
    {
        $normalized = strtoupper(trim((string) $pin));

        if ($normalized === '' || ! $this->access_pin) {
            return false;
        }

        return Hash::check($normalized, $this->access_pin);
    }

    public function isConfidential(): bool
    {
        return $this->privacy === 'confidential';
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
        return match ($this->privacy) {
            'anonymous' => 'Pelapor Anonim',
            'confidential' => 'Pelapor Rahasia',
            default => $this->nama_pelapor,
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'bg-[#fef3c7] text-[#92400e]',
            'diverifikasi' => 'bg-[#e0f2fe] text-[#075985]',
            'diproses' => 'bg-[#eff6ff] text-[#1e3a8a]',
            'selesai' => 'bg-[#dcfce7] text-[#166534]',
            'ditolak' => 'bg-[#fee2e2] text-[#991b1b]',
            default => 'bg-[#f8fafc] text-[#475569]',
        };
    }

    public function getWhatsappUrlAttribute(): ?string
    {
        $digits = preg_replace('/\D+/', '', $this->no_wa ?? '');
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (! str_starts_with($digits, '62')) {
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
