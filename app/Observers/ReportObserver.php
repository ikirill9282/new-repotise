<?php

namespace App\Observers;

use App\Models\Report;
use App\Models\ModerationQueue;

class ReportObserver
{
    /**
     * Handle the Report "created" event.
     */
    public function created(Report $report): void
    {
        // Добавляем в очередь модерации, если это новая жалоба или error report
        if ($report->status === Report::STATUS_NEW && 
            ($report->type === Report::TYPE_COMPLAINT || $report->type === Report::TYPE_CONTENT_ERROR)) {
            
            $reason = $report->type === Report::TYPE_COMPLAINT 
                ? 'User complaint' 
                : 'Content error report';
            
            $priority = $report->type === Report::TYPE_COMPLAINT 
                ? ModerationQueue::PRIORITY_HIGH 
                : ModerationQueue::PRIORITY_NORMAL;
            
            ModerationQueue::add('report', $report->id, $reason, $priority);
        }
    }

    /**
     * Handle the Report "updated" event.
     */
    public function updated(Report $report): void
    {
        // Если статус изменился на resolved, удаляем из очереди
        if ($report->wasChanged('status') && $report->status === Report::STATUS_RESOLVED) {
            ModerationQueue::remove('report', $report->id);
        }
        
        // Если статус изменился с new на другой, также удаляем
        if ($report->wasChanged('status') && 
            $report->getOriginal('status') === Report::STATUS_NEW && 
            $report->status !== Report::STATUS_NEW) {
            ModerationQueue::remove('report', $report->id);
        }
    }

    /**
     * Handle the Report "deleted" event.
     */
    public function deleted(Report $report): void
    {
        ModerationQueue::remove('report', $report->id);
    }
}
