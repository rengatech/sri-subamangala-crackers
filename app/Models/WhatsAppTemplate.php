<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppTemplate extends Model
{
    protected $table = 'whatsapp_templates';

    protected $fillable = [
        'meta_template_id',
        'template_name',
        'body',
        'variables',
        'language_code',
        'category',
        'components',
        'status',
        'quality_score',
        'rejected_reason',
    ];

    protected $casts = [
        'variables' => 'array',
        'components' => 'array',
    ];

    /**
     * All possible Meta template statuses.
     */
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_IN_APPEAL = 'IN_APPEAL';
    const STATUS_PENDING = 'PENDING';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_PENDING_DELETION = 'PENDING_DELETION';
    const STATUS_DELETED = 'DELETED';
    const STATUS_DISABLED = 'DISABLED';
    const STATUS_PAUSED = 'PAUSED';
    const STATUS_LIMIT_EXCEEDED = 'LIMIT_EXCEEDED';

    const STATUSES = [
        self::STATUS_APPROVED,
        self::STATUS_IN_APPEAL,
        self::STATUS_PENDING,
        self::STATUS_REJECTED,
        self::STATUS_PENDING_DELETION,
        self::STATUS_DELETED,
        self::STATUS_DISABLED,
        self::STATUS_PAUSED,
        self::STATUS_LIMIT_EXCEEDED,
    ];

    /**
     * Statuses that allow sending the template.
     */
    const SENDABLE_STATUSES = [
        self::STATUS_APPROVED,
    ];

    /**
     * Statuses that allow editing the template.
     */
    const EDITABLE_STATUSES = [
        self::STATUS_APPROVED,
        self::STATUS_REJECTED,
        self::STATUS_PAUSED,
    ];

    const REJECTED_REASONS = [
        'ABUSIVE_CONTENT',
        'INVALID_FORMAT',
        'NONE',
        'PROMOTIONAL',
        'TAG_CONTENT_MISMATCH',
        'SCAM',
    ];

    const QUALITY_SCORES = [
        'GREEN' => 'High',
        'YELLOW' => 'Medium',
        'RED' => 'Low',
        'UNKNOWN' => 'Pending',
    ];

    public function isSendable(): bool
    {
        return in_array(strtoupper($this->status), self::SENDABLE_STATUSES);
    }

    public function isEditable(): bool
    {
        return in_array(strtoupper($this->status), self::EDITABLE_STATUSES);
    }
}
