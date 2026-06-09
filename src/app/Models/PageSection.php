<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSection extends Model
{
    protected $casts = [
        'data' => 'array',
        'enabled' => 'boolean',
    ];

    public function getLocaleData(?string $locale = null): array
    {
        // Locale parameter retained for backwards compatibility; sections are now single-locale.
        // User-facing strings should be translated via __() in the theme blades.
        return $this->data ?? [];
    }

    public function sectionConfig(): array
    {
        return config('sections.'.$this->type, []);
    }

    public function renderFrontend(int $index = 0): string
    {
        return view('theme::sections.'.$this->type, [
            'section'    => $this,
            'sectionBg'  => $index % 2 === 0 ? 'bg-cream-50' : 'bg-cream-100',
            'sectionBg2' => $index % 2 === 0 ? 'bg-cream-100' : 'bg-cream-50',
        ])->render();
    }

    public function renderAdmin(): string
    {
        return view('admin.cms.sections.'.$this->type, ['section' => $this])->render();
    }
}
