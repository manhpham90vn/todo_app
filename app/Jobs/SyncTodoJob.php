<?php

namespace App\Jobs;

use App\Models\CalendarEvent;
use App\Models\Todo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncTodoJob implements ShouldQueue
{
    use Queueable;

    public int $userId;

    /** @var CalendarEvent[] */
    public array $calendarEvents;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, array $calendarEvents)
    {
        $this->userId = $userId;
        $this->calendarEvents = $calendarEvents;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->calendarEvents as $event) {
            $priority = 'medium';
            if ($event->summary && preg_match('/\b(deploy|release)\b/i', $event->summary)) {
                $priority = 'high';
            }

            \Log::info("Syncing Todo for User ID: {$this->userId}, Event ID: {$event->google_event_id}, Title: {$event->summary}, Priority: {$priority}");

            Todo::updateOrCreate(
                [
                    'user_id' => $this->userId,
                    'external_id' => $event->google_event_id,
                ],
                [
                    'title' => $event->summary,
                    'description' => $event->description,
                    'priority' => $priority,
                    'start_at' => $event->start_at,
                    'end_at' => $event->end_at,
                ]
            );
        }
    }
}
