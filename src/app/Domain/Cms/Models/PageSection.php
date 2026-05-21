<?php

namespace App\Domain\Cms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSection extends Model
{
    protected $casts = [
        'data' => 'array',
        'enabled' => 'boolean',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function getLocaleData(?string $locale = null): array
    {
        $locale ??= app()->getLocale();
        $data = $this->data ?? [];

        return $data[$locale] ?? $data['en'] ?? [];
    }

    public function sectionConfig(): array
    {
        return config('sections.'.$this->type, []);
    }

    public function renderFrontend(): string
    {
        return view('theme::sections.'.$this->type, ['section' => $this])->render();
    }

    public function renderAdmin(): string
    {
        return view('admin.cms.sections.'.$this->type, ['section' => $this])->render();
    }
}
