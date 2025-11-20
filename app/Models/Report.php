<?php

namespace App\Models;

use App\Traits\HasAuthor;
use App\Traits\HasReport;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    use HasAuthor, HasReport;

    protected $fillable = [
        'user_id',
        'type',
        'reason',
        'message',
        'status',
        'resolution_type',
        'resolved_by',
        'resolved_at',
        'resolution_note',
        'reportable_type',
        'reportable_id',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public const STATUS_NEW = 'new';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED = 'resolved';

    public const TYPE_COMPLAINT = 'complaint';
    public const TYPE_CONTENT_ERROR = 'content_error';

    public const REASON_SPAM_OR_SCAM = 'Spam or Scam';
    public const REASON_OFFENSIVE = 'Offensive or abusive';
    public const REASON_INAPPROPRIATE = 'Inappropriate content';

    public const RESOLUTION_ACTION_TAKEN = 'action_taken';
    public const RESOLUTION_DISMISSED = 'dismissed';

    public function reportable()
    {
      return $this->morphTo();
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function resolve(?int $userId = null, ?string $note = null, ?string $resolutionType = null): void
    {
        $this->update([
            'status' => self::STATUS_RESOLVED,
            'resolution_type' => $resolutionType ?? self::RESOLUTION_ACTION_TAKEN,
            'resolved_by' => $userId ?? auth()->id(),
            'resolved_at' => now(),
            'resolution_note' => $note,
        ]);
    }

    public function dismiss(?int $userId = null, ?string $note = null): void
    {
        $this->update([
            'status' => self::STATUS_RESOLVED,
            'resolution_type' => self::RESOLUTION_DISMISSED,
            'resolved_by' => $userId ?? auth()->id(),
            'resolved_at' => now(),
            'resolution_note' => $note,
        ]);
    }

    public function getDisplayStatus(): string
    {
        if ($this->status === self::STATUS_NEW) {
            return 'New';
        }
        
        if ($this->status === self::STATUS_RESOLVED) {
            if ($this->resolution_type === self::RESOLUTION_ACTION_TAKEN) {
                return 'Resolved - Action Taken';
            }
            if ($this->resolution_type === self::RESOLUTION_DISMISSED) {
                return 'Resolved - Complaint Dismissed';
            }
            return 'Resolved';
        }
        
        return ucfirst($this->status);
    }

    public function isComplaint(): bool
    {
        return $this->type === self::TYPE_COMPLAINT;
    }

    public function isContentError(): bool
    {
        return $this->type === self::TYPE_CONTENT_ERROR;
    }

    public function setInProgress(?int $userId = null): void
    {
        $this->update([
            'status' => self::STATUS_IN_PROGRESS,
        ]);
    }
}
