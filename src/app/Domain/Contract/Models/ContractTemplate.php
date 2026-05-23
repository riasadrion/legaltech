<?php

namespace App\Domain\Contract\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractTemplate extends Model
{
    protected $fillable = [
        'category_id', 'name', 'description', 'body',
        'ai_system_prompt', 'credits_cost', 'active', 'sort',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ContractTemplateCategory::class, 'category_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(ContractTemplateField::class, 'template_id')->orderBy('sort');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'template_id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
