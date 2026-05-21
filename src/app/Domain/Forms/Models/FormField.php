<?php

namespace App\Domain\Forms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    protected $fillable = [
        'form_id', 'name', 'type', 'label', 'placeholder',
        'options', 'required', 'validation', 'sort',
    ];

    protected $casts = [
        'label' => 'array',
        'placeholder' => 'array',
        'options' => 'array',
        'required' => 'boolean',
    ];

    // Supported types
    const TYPE_TEXT = 'text';

    const TYPE_NUMBER = 'number';

    const TYPE_EMAIL = 'email';

    const TYPE_SELECT = 'select';

    const TYPE_TEXTAREA = 'textarea';

    const TYPE_FILE = 'file';

    const TYPE_CHECKBOX = 'checkbox';

    public static function types(): array
    {
        return [self::TYPE_TEXT, self::TYPE_NUMBER, self::TYPE_EMAIL, self::TYPE_SELECT, self::TYPE_TEXTAREA, self::TYPE_FILE, self::TYPE_CHECKBOX];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function getLocaleLabel(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $label = $this->label;

        return $label[$locale] ?? $label['en'] ?? $this->name;
    }

    public function getLocalePlaceholder(?string $locale = null): ?string
    {
        if (! $this->placeholder) {
            return null;
        }
        $locale = $locale ?: app()->getLocale();

        return $this->placeholder[$locale] ?? $this->placeholder['en'] ?? null;
    }
}
