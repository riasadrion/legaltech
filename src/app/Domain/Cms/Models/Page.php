<?php

namespace App\Domain\Cms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $casts = [
        'title' => 'array',
        'meta_title' => 'array',
        'meta_description' => 'array',
        'is_system' => 'boolean',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(PageSection::class)->orderBy('sort');
    }

    public function getLocalTitle(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $title = $this->title;

        return $title[$locale] ?? $title['en'] ?? '';
    }

    public function getLocalMetaTitle(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $meta = $this->meta_title ?? [];

        return $meta[$locale] ?? $meta['en'] ?? $this->getLocalTitle($locale);
    }

    public function getLocalMetaDescription(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $meta = $this->meta_description ?? [];

        return $meta[$locale] ?? $meta['en'] ?? '';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
