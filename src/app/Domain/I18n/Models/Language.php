<?php

namespace App\Domain\I18n\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = [
        'name',
        'native_name',
        'code',
        'direction',
        'is_default',
        'is_active',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function isRtl(): bool
    {
        return $this->direction === 'rtl';
    }

    public function scopeActive($query): mixed
    {
        return $query->where('is_active', true);
    }

    public static function default(): ?self
    {
        return static::where('is_default', true)->where('is_active', true)->first();
    }

    public static function findByCode(string $code): ?self
    {
        return static::where('code', $code)->where('is_active', true)->first();
    }
}
