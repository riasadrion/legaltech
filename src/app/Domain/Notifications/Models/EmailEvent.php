<?php

namespace App\Domain\Notifications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailEvent extends Model
{
    const EVENT_DELIVERED = 'delivered';

    const EVENT_OPENED = 'opened';

    const EVENT_CLICKED = 'clicked';

    const EVENT_BOUNCED = 'bounced';

    const EVENT_COMPLAINED = 'complained';

    protected $fillable = [
        'notification_log_id', 'provider', 'message_id',
        'email', 'event', 'payload', 'occurred_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function log(): BelongsTo
    {
        return $this->belongsTo(NotificationLog::class, 'notification_log_id');
    }
}
