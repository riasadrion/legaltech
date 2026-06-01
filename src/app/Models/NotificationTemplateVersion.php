<?php

namespace App\Models;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationTemplateVersion extends Model
{
    protected $fillable = [
        'notification_template_id', 'created_by', 'version',
        'subject', 'email_body', 'sms_body',
    ];

    protected $casts = [
        'subject' => 'array',
        'email_body' => 'array',
        'sms_body' => 'array',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class, 'notification_template_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
