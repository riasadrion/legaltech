<?php

namespace App\Domain\Contract\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractTemplateField extends Model
{
    protected $fillable = [
        'template_id', 'label', 'key', 'type',
        'placeholder', 'group', 'options', 'required', 'sort',
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(ContractTemplate::class, 'template_id');
    }
}
