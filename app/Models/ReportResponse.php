<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'report_id',
    'user_id',
    'sender_type',
    'sender_name',
    'message',
    'attachment_path',
    'attachment_name',
    'status_from',
    'status_to',
    'is_internal',
])]
class ReportResponse extends Model
{
    protected function casts(): array
    {
        return [
            'is_internal' => 'boolean',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
