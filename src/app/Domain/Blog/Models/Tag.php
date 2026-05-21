<?php

namespace App\Domain\Blog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $casts = [
        'name' => 'array',
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(BlogPost::class, 'blog_post_tag');
    }

    public function getLocalName(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $name = $this->name;

        return $name[$locale] ?? $name['en'] ?? '';
    }
}
