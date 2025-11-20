<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModerationQueue extends Model
{
    protected $fillable = [
        'reason',
        'priority',
        'model',
        'model_id',
    ];

    public const PRIORITY_LOW = 1;
    public const PRIORITY_NORMAL = 2;
    public const PRIORITY_HIGH = 3;
    public const PRIORITY_URGENT = 4;

    /**
     * Get the model instance
     */
    public function getModelInstance()
    {
        $modelClass = $this->getModelClass();
        if (!$modelClass) {
            return null;
        }
        
        return $modelClass::find($this->model_id);
    }

    /**
     * Get model class from model string
     */
    protected function getModelClass(): ?string
    {
        return match($this->model) {
            'article' => Article::class,
            'comment' => Comment::class,
            'review' => Review::class,
            'report' => Report::class,
            default => class_exists($this->model) ? $this->model : null,
        };
    }

    /**
     * Add item to moderation queue
     */
    public static function add(string $model, int $modelId, string $reason, int $priority = self::PRIORITY_NORMAL): self
    {
        // Check if already exists
        $existing = static::where('model', $model)
            ->where('model_id', $modelId)
            ->first();
        
        if ($existing) {
            return $existing;
        }

        return static::create([
            'model' => $model,
            'model_id' => $modelId,
            'reason' => $reason,
            'priority' => $priority,
        ]);
    }

    /**
     * Remove item from moderation queue
     */
    public static function remove(string $model, int $modelId): bool
    {
        return static::where('model', $model)
            ->where('model_id', $modelId)
            ->delete() > 0;
    }
}
