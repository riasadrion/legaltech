<?php

namespace App\Domain\Notifications\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationTemplate extends Model
{
    protected $fillable = [
        'key', 'name', 'description', 'channels',
        'subject', 'email_body', 'sms_body',
        'variables', 'is_active', 'is_system',
    ];

    protected $casts = [
        'channels' => 'array',
        'subject' => 'array',
        'email_body' => 'array',
        'sms_body' => 'array',
        'variables' => 'array',
        'is_active' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function versions(): HasMany
    {
        return $this->hasMany(NotificationTemplateVersion::class);
    }

    public function getLocalSubject(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $subject = $this->subject ?? [];

        return $subject[$locale] ?? $subject['en'] ?? '';
    }

    public function getLocalEmailBody(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $body = $this->email_body ?? [];

        return $body[$locale] ?? $body['en'] ?? '';
    }

    public function getLocalSmsBody(?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $body = $this->sms_body ?? [];

        return $body[$locale] ?? $body['en'] ?? '';
    }

    public function renderSubject(array $vars, ?string $locale = null): string
    {
        return $this->interpolate($this->getLocalSubject($locale), $vars);
    }

    public function renderEmailBody(array $vars, ?string $locale = null): string
    {
        return $this->interpolate($this->getLocalEmailBody($locale), $vars);
    }

    public function renderSmsBody(array $vars, ?string $locale = null): string
    {
        return $this->interpolate($this->getLocalSmsBody($locale), $vars);
    }

    private function interpolate(string $template, array $vars): string
    {
        foreach ($vars as $key => $value) {
            $template = str_replace('{{'.$key.'}}', (string) $value, $template);
        }

        return $template;
    }

    public function getChannelsAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        $decoded = json_decode($value ?? '[]', true);

        return is_array($decoded) ? $decoded : ['email'];
    }

    public function getVariablesAttribute($value): array
    {
        if (is_array($value)) {
            return $value;
        }
        $decoded = json_decode($value ?? '[]', true);

        return is_array($decoded) ? $decoded : [];
    }

    public function hasChannel(string $channel): bool
    {
        return in_array($channel, $this->channels, true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
