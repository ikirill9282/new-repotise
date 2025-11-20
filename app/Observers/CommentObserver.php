<?php

namespace App\Observers;

use App\Models\Comment;
use App\Models\ModerationQueue;
use App\Enums\Status;

class CommentObserver
{
    /**
     * Handle the Comment "created" event.
     */
    public function created(Comment $comment): void
    {
        $this->handleStatusChange($comment);
    }

    /**
     * Handle the Comment "updated" event.
     */
    public function updated(Comment $comment): void
    {
        // Проверяем, изменился ли статус
        if ($comment->wasChanged('status_id')) {
            $this->handleStatusChange($comment);
        }
    }

    /**
     * Handle the Comment "deleted" event.
     */
    public function deleted(Comment $comment): void
    {
        ModerationQueue::remove('comment', $comment->id);
    }

    /**
     * Handle status change and manage moderation queue
     */
    protected function handleStatusChange(Comment $comment): void
    {
        // Статус Pending Approval (Pending Review = 3)
        if ($comment->status_id == Status::PENDING) {
            // Добавляем в очередь модерации
            ModerationQueue::add(
                'comment', 
                $comment->id, 
                'New comment pending approval',
                ModerationQueue::PRIORITY_NORMAL
            );
        } else {
            // При любом другом статусе (Published, Rejected, Spam) удаляем из очереди
            ModerationQueue::remove('comment', $comment->id);
        }
    }
}
