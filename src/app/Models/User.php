<?php

namespace App\Models;

use App\Concerns\HasRoles;
use App\Domain\Payment\Models\Payment;

use App\Concerns\LogsActivity;
use App\Domain\Subscription\Models\UserSubscription;
use App\Notifications\Auth\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasRoles, LogsActivity, Notifiable;

    protected $guard = 'user';

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'mobile_country_code',
        'mobile',
        'country_code',
        'address',
        'city',
        'state',
        'zip',
        'avatar_path',
        'locale',
        'timezone',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'mobile_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_enabled' => 'boolean',
            'two_factor_recovery_codes' => 'array',
            'balance' => 'float',
        ];
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isBanned(): bool
    {
        return $this->status === 'banned';
    }

    public function logins()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function subscription()
    {
        return $this->hasOne(UserSubscription::class)
            ->latestOfMany();
    }

    public function hasActivePlan(): bool
    {
        return $this->subscription?->isActive() ?? false;
    }

    public function canGenerateAiContract(): bool
    {
        $sub = $this->subscription;
        return $sub && $sub->isActive() && (bool) ($sub->plan->ai_contract_generation ?? false);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

}
