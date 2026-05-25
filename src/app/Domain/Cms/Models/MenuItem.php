<?php

namespace App\Domain\Cms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuItem extends Model
{
    protected $casts = [
        'label' => 'array',
        'open_in_new_tab' => 'boolean',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function getLocalLabel(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $label = $this->label;

        return $label[$locale] ?? $label['en'] ?? '';
    }

    public function resolvedUrl(): string
    {
        if ($this->page_id && $this->page) {
            return url('/'.$this->page->slug);
        }

        $raw = $this->url ?? '#';

        if ($raw === '#' || $raw === '' || str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://') || str_starts_with($raw, 'mailto:') || str_starts_with($raw, 'tel:')) {
            return $raw;
        }

        return url($raw);
    }
}
