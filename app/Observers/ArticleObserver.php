<?php

namespace App\Observers;

use App\Models\Article;
use App\Models\ModerationQueue;
use App\Enums\Status;

class ArticleObserver
{
    /**
     * Handle the Article "created" event.
     */
    public function created(Article $article): void
    {
        $this->handleStatusChange($article);
    }

    /**
     * Handle the Article "updated" event.
     */
    public function updated(Article $article): void
    {
        // Проверяем, изменился ли статус
        if ($article->wasChanged('status_id')) {
            $this->handleStatusChange($article);
        }
    }

    /**
     * Handle the Article "deleted" event.
     */
    public function deleted(Article $article): void
    {
        ModerationQueue::remove('article', $article->id);
    }

    /**
     * Handle status change and manage moderation queue
     */
    protected function handleStatusChange(Article $article): void
    {
        // Статусы, требующие модерации
        $needsModeration = [
            Status::PENDING, // Pending Review (3)
            Status::REVISION, // Needs Revision (4)
        ];

        // Статусы, которые завершают модерацию
        $moderationComplete = [
            Status::ACTIVE, // Published (1)
            Status::REJECT, // Reject (5)
        ];

        if (in_array($article->status_id, $needsModeration)) {
            // Добавляем в очередь модерации
            $reason = $article->status_id == Status::PENDING 
                ? 'New article pending review' 
                : 'Article needs revision';
            
            ModerationQueue::add('article', $article->id, $reason, ModerationQueue::PRIORITY_NORMAL);
        } elseif (in_array($article->status_id, $moderationComplete)) {
            // Удаляем из очереди модерации
            ModerationQueue::remove('article', $article->id);
        }
    }
}
