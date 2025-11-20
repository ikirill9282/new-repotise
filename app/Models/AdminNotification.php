<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\Product;
use App\Models\Article;
use App\Models\Review;

class AdminNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'severity',
        'read',
        'read_by',
        'read_at',
        'notifiable_type',
        'notifiable_id',
        'data',
    ];

    protected $casts = [
        'read' => 'boolean',
        'read_at' => 'datetime',
        'data' => 'array',
    ];

    public const TYPE_COMPLAINT = 'complaint';
    public const TYPE_MODERATION = 'moderation';
    public const TYPE_SYSTEM_ERROR = 'system_error';

    public const SEVERITY_INFO = 'info';
    public const SEVERITY_WARNING = 'warning';
    public const SEVERITY_ERROR = 'error';
    public const SEVERITY_CRITICAL = 'critical';

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    public function readBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'read_by');
    }

    public function markAsRead(?int $userId = null): void
    {
        $this->update([
            'read' => true,
            'read_by' => $userId ?? auth()->id(),
            'read_at' => now(),
        ]);
    }

    public function getUrlAttribute(): ?string
    {
        if (!$this->notifiable) {
            return null;
        }

        return match($this->type) {
            self::TYPE_COMPLAINT => route('filament.admin.resources.reports.index'),
            self::TYPE_MODERATION => $this->getModerationUrl(),
            default => null,
        };
    }

    protected function getModerationUrl(): ?string
    {
        $model = $this->notifiable;
        if (!$model) {
            return null;
        }

        return match(get_class($model)) {
            Product::class => route('filament.admin.resources.products.index', ['tableFilters' => ['status_id' => ['value' => \App\Enums\Status::PENDING]]]),
            Article::class => route('filament.admin.resources.articles.index', ['tableFilters' => ['status_id' => ['value' => \App\Enums\Status::PENDING]]]),
            Review::class => route('filament.admin.resources.reviews.index', ['tableFilters' => ['status_id' => ['value' => \App\Enums\Status::PENDING]]]),
            default => null,
        };
    }
}
