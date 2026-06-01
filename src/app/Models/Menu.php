<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $casts = [
        'name' => 'array',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class)->whereNull('parent_id')->orderBy('sort')->with('children');
    }

    public function allItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('sort');
    }

    public function getLocalName(?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $name = $this->name;

        return $name[$locale] ?? $name['en'] ?? $this->handle;
    }

    public static function handle(string $handle): ?self
    {
        return static::where('handle', $handle)->first();
    }
}
