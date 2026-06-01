<?php

namespace App\Concerns;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    public function activity()
    {
        return $this->morphMany(ActivityLog::class, 'subject');
    }

    public function logActivity(string $action, array $properties = [], ?Model $causer = null): ActivityLog
    {
        $request = request();

        return ActivityLog::create([
            'causer_type' => $causer ? get_class($causer) : null,
            'causer_id' => $causer?->getKey(),
            'subject_type' => get_class($this),
            'subject_id' => $this->getKey(),
            'action' => $action,
            'properties' => $properties,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'created_at' => now(),
        ]);
    }
}
