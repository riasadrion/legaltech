<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    const STATUS_DRAFT = 'draft';

    const STATUS_SCHEDULED = 'scheduled';

    const STATUS_PUBLISHED = 'published';

    protected $casts = [
        'title' => 'array',
        'excerpt' => 'array',
        'body' => 'array',
        'meta_title' => 'array',
        'meta_description' => 'array',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'author_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'blog_post_tag');
    }

    // Only return posts that are published and published_at <= now()
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where('published_at', '<=', now());
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    public function isLive(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && $this->published_at !== null
            && $this->published_at->lte(now());
    }

    public function getLocalTitle(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $title = $this->title;

        return $title[$locale] ?? $title['en'] ?? '';
    }

    public function getLocalExcerpt(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $excerpt = $this->excerpt ?? [];

        return $excerpt[$locale] ?? $excerpt['en'] ?? '';
    }

    public function getLocalBody(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $body = $this->body ?? [];

        return $body[$locale] ?? $body['en'] ?? '';
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

        return $meta[$locale] ?? $meta['en'] ?? $this->getLocalExcerpt($locale);
    }

    public static function generateSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
