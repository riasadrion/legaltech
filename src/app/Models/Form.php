<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Form extends Model
{
    protected $fillable = ['key', 'name', 'context'];

    protected $casts = ['name' => 'array'];

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('sort');
    }

    public function getLocaleName(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $name = $this->name;

        return $name[$locale] ?? $name['en'] ?? '';
    }
}
