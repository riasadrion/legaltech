<?php

namespace App\Domain\Support\Models;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SupportTicket extends Model
{
    const STATUS_OPEN = 'open';

    const STATUS_IN_PROGRESS = 'in_progress';

    const STATUS_WAITING = 'waiting';

    const STATUS_CLOSED = 'closed';

    const PRIORITY_LOW = 'low';

    const PRIORITY_NORMAL = 'normal';

    const PRIORITY_HIGH = 'high';

    const PRIORITY_URGENT = 'urgent';

    const CATEGORY_GENERAL = 'general';

    const CATEGORY_BILLING = 'billing';

    const CATEGORY_TECHNICAL = 'technical';

    const CATEGORY_KYC = 'kyc';

    const CATEGORY_OTHER = 'other';

    protected $fillable = [
        'reference', 'user_id', 'assigned_to',
        'guest_email', 'guest_name', 'guest_verify_token', 'guest_verified',
        'subject', 'category', 'priority', 'status', 'closed_at',
    ];

    protected $casts = [
        'guest_verified' => 'boolean',
        'closed_at' => 'datetime',
    ];

    public static function generateReference(): string
    {
        do {
            $ref = strtoupper(Str::random(8));
        } while (static::where('reference', $ref)->exists());

        return $ref;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class);
    }

    public function publicMessages(): HasMany
    {
        return $this->hasMany(SupportMessage::class)->where('is_internal', false);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(SupportAttachment::class, 'support_message_id');
    }

    public function isOpen(): bool
    {
        return $this->status !== self::STATUS_CLOSED;
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function isGuest(): bool
    {
        return $this->user_id === null;
    }

    public function getAuthorName(): string
    {
        if ($this->user) {
            return $this->user->name ?? $this->user->username ?? $this->user->email;
        }

        return $this->guest_name ?? $this->guest_email ?? 'Guest';
    }

    public function getAuthorEmail(): string
    {
        return $this->user?->email ?? $this->guest_email ?? '';
    }

    public function scopeOpen($query)
    {
        return $query->where('status', '!=', self::STATUS_CLOSED);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public static function priorities(): array
    {
        return [self::PRIORITY_LOW, self::PRIORITY_NORMAL, self::PRIORITY_HIGH, self::PRIORITY_URGENT];
    }

    public static function categories(): array
    {
        return [self::CATEGORY_GENERAL, self::CATEGORY_BILLING, self::CATEGORY_TECHNICAL, self::CATEGORY_KYC, self::CATEGORY_OTHER];
    }

    public static function statuses(): array
    {
        return [self::STATUS_OPEN, self::STATUS_IN_PROGRESS, self::STATUS_WAITING, self::STATUS_CLOSED];
    }
}
