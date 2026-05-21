<?php

namespace App\Domain\Blog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends Model
{
    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'meta_title' => 'array',
        'meta_description' => 'array',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    public function getLocalName(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $name = $this->name;

        return $name[$locale] ?? $name['en'] ?? '';
    }

    public function getLocalDescription(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $desc = $this->description ?? [];

        return $desc[$locale] ?? $desc['en'] ?? '';
    }
}
