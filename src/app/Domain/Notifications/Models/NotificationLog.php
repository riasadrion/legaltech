<?php

namespace App\Domain\Notifications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationLog extends Model
{
    const STATUS_SENT = 'sent';

    const STATUS_FAILED = 'failed';

    const STATUS_SKIPPED = 'skipped';

    protected $fillable = [
        'notification_key', 'channel',
        'notifiable_type', 'notifiable_id',
        'recipient', 'subject', 'body',
        'status', 'error', 'locale',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function emailEvents(): HasMany
    {
        return $this->hasMany(EmailEvent::class);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }
}
