<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $casts = [
        'title' => 'array',
        'meta_title' => 'array',
        'meta_description' => 'array',
        'is_system' => 'boolean',
        'section_ids' => 'array',
    ];

    public function sections(): Collection
    {
        $ids = $this->section_ids ?? [];
        if (empty($ids)) {
            return new Collection;
        }

        $sections = PageSection::whereIn('id', $ids)->get()->keyBy('id');

        $ordered = collect($ids)->map(fn ($id) => $sections->get($id))->filter()->values();

        return new Collection($ordered);
    }

    public function enabledSections(): Collection
    {
        return $this->sections()->filter(fn ($s) => $s->enabled)->values();
    }

    public function addSection(PageSection $section): void
    {
        $ids = $this->section_ids ?? [];
        $ids[] = $section->id;
        $this->section_ids = $ids;
        $this->save();
    }

    public function removeSection(int $sectionId): void
    {
        $this->section_ids = array_values(array_filter(
            $this->section_ids ?? [],
            fn ($id) => $id !== $sectionId,
        ));
        $this->save();
    }

    public function reorderSections(array $orderedIds): void
    {
        $current = $this->section_ids ?? [];
        $valid = array_filter($orderedIds, fn ($id) => in_array($id, $current));
        $this->section_ids = array_values($valid);
        $this->save();
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
