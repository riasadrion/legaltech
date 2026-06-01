<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSuppression extends Model
{
    const REASON_BOUNCE = 'bounce';

    const REASON_COMPLAINT = 'complaint';

    const REASON_MANUAL = 'manual';

    const REASON_UNSUBSCRIBE = 'unsubscribe';

    protected $fillable = ['email', 'reason', 'source', 'meta'];

    protected $casts = ['meta' => 'array'];

    public static function isSuppressed(string $email): bool
    {
        return static::where('email', strtolower($email))->exists();
    }

    public static function suppress(string $email, string $reason, string $source = 'manual', array $meta = []): void
    {
        static::updateOrCreate(
            ['email' => strtolower($email)],
            ['reason' => $reason, 'source' => $source, 'meta' => $meta]
        );
    }
}
