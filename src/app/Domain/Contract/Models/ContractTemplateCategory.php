<?php

namespace App\Domain\Contract\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractTemplateCategory extends Model
{
    protected $fillable = ['name', 'slug', 'icon', 'sort'];

    public function templates(): HasMany
    {
        return $this->hasMany(ContractTemplate::class, 'category_id');
    }
}
