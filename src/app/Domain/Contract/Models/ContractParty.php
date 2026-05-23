<?php

namespace App\Domain\Contract\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ContractParty extends Model
{
    protected $fillable = [
        'contract_id', 'name', 'email', 'role',
        'token', 'signed_at', 'ip_address', 'user_agent', 'sort',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function hasSigned(): bool
    {
        return !is_null($this->signed_at);
    }

    public function generateToken(): string
    {
        $this->token = Str::random(64);
        $this->save();
        return $this->token;
    }
}
