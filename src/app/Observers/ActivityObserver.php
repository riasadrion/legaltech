<?php

namespace App\Observers;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityObserver
{
    public function created(Model $model): void
    {
        $this->record($model, 'created');
    }

    public function updated(Model $model): void
    {
        $dirty = $model->getDirty();
        unset($dirty['updated_at'], $dirty['last_seen_at'], $dirty['remember_token']);

        if (empty($dirty)) {
            return;
        }

        $original = array_intersect_key($model->getOriginal(), $dirty);

        // Mask sensitive fields
        foreach (['password', 'remember_token', 'two_factor_secret'] as $field) {
            if (isset($dirty[$field])) {
                $dirty[$field] = '***';
            }
            if (isset($original[$field])) {
                $original[$field] = '***';
            }
        }

        $this->record($model, 'updated', [
            'old' => $original,
            'attributes' => $dirty,
        ]);
    }

    public function deleted(Model $model): void
    {
        $this->record($model, 'deleted');
    }

    private function record(Model $model, string $action, array $properties = []): void
    {
        try {
            $causer = auth('admin')->user() ?? auth('web')->user();

            ActivityLog::create([
                'causer_type' => $causer ? get_class($causer) : null,
                'causer_id' => $causer?->getKey(),
                'subject_type' => get_class($model),
                'subject_id' => $model->getKey(),
                'action' => $action,
                'properties' => $properties,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'created_at' => now(),
            ]);
        } catch (\Throwable) {
            // Never let logging break the app
        }
    }
}
