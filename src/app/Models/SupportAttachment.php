<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportAttachment extends Model
{
    protected $fillable = [
        'support_message_id', 'original_name',
        'disk', 'path', 'mime_type', 'size',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(SupportMessage::class, 'support_message_id');
    }

    public function signedUrl(int $expiryMinutes = 30): string
    {
        return route('support.attachment.download', [
            'attachment' => $this->id,
            'signature' => hash_hmac('sha256', (string) $this->id, config('app.key')),
            'expires' => now()->addMinutes($expiryMinutes)->timestamp,
        ]);
    }

    public function formattedSize(): string
    {
        $bytes = $this->size;
        if ($bytes < 1024) {
            return $bytes.' B';
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 1).' KB';
        }

        return round($bytes / 1048576, 1).' MB';
    }
}
