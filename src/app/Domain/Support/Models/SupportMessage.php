<?php

namespace App\Domain\Support\Models;

use App\Services\HtmlPurifier;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportMessage extends Model
{
    protected $fillable = [
        'support_ticket_id', 'author_type', 'author_id',
        'body', 'is_internal',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    protected static $allowedTags = [
        'p', 'br', 'strong', 'em', 'u',
        'ul', 'ol', 'li',
        'a[href|title|rel]',
        'blockquote',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'support_ticket_id');
    }

    public function author()
    {
        return $this->morphTo();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(SupportAttachment::class);
    }

    public function setBodyAttribute(string $value): void
    {
        $purifier = new HtmlPurifier;
        $this->attributes['body'] = $purifier->clean($value, self::$allowedTags);
    }

    public function getAuthorName(): string
    {
        $author = $this->author;
        if ($author === null) {
            return 'System';
        }

        return $author->name ?? $author->username ?? $author->email ?? 'Unknown';
    }

    public function isFromAdmin(): bool
    {
        return $this->author_type === Admin::class;
    }

    public function isFromUser(): bool
    {
        return $this->author_type === User::class;
    }
}
