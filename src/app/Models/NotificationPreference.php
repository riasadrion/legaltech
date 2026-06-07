<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = ['user_id', 'notification_key', 'channel', 'enabled'];

    protected $casts = ['enabled' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function isEnabled(int $userId, string $key, string $channel): bool
    {
        $pref = static::where('user_id', $userId)
            ->where('notification_key', $key)
            ->where('channel', $channel)
            ->first();

        return $pref === null || $pref->enabled;
    }
}
