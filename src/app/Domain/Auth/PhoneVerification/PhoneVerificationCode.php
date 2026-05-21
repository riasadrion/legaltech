<?php

namespace App\Domain\Auth\PhoneVerification;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PhoneVerificationCode extends Model
{
    protected $table = 'phone_verification_codes';

    protected $fillable = [
        'user_id',
        'phone',
        'code_hash',
        'attempts',
        'expires_at',
        'verified_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'attempts' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
}
