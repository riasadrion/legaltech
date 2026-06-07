<?php

namespace App\Domain\Contract\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Contract extends Model
{
    protected $fillable = [
        'user_id', 'template_id', 'title', 'field_data',
        'body_html', 'status', 'share_token',
        'credits_used', 'expires_at',
    ];

    protected $casts = [
        'field_data' => 'array',
        'expires_at' => 'datetime',
    ];

    const STATUS_DRAFT = 'draft';

    const STATUS_GENERATING = 'generating';

    const STATUS_EDITING = 'editing';

    const STATUS_READY = 'ready';

    const STATUS_SENT = 'sent';

    const STATUS_SIGNED = 'signed';

    const STATUS_EXPIRED = 'expired';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ContractTemplate::class, 'template_id');
    }

    public function parties(): HasMany
    {
        return $this->hasMany(ContractParty::class)->orderBy('sort');
    }

    public function signers(): HasMany
    {
        return $this->parties()->where('role', 'signer');
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_EDITING]);
    }

    public function isReady(): bool
    {
        return $this->status === self::STATUS_READY;
    }

    public function isSigned(): bool
    {
        return $this->status === self::STATUS_SIGNED;
    }

    public function allPartiesSigned(): bool
    {
        return $this->signers()->whereNull('signed_at')->doesntExist();
    }

    public function generateShareToken(): string
    {
        $this->share_token = Str::random(48);
        $this->save();

        return $this->share_token;
    }
}
