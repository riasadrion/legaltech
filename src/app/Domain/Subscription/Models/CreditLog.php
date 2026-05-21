<?php

namespace App\Domain\Subscription\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CreditLog extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'amount',
        'description',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(UserSubscription::class);
    }
}
