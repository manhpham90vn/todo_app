<?php

namespace App\Jobs;

use App\Models\Todo;
use App\Notifications\BrowserPush;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendWebPush implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $now = now();
        $toHour = $now->copy()->addMinutes(15);

        $nowInUTC = now()->utc();
        $toHourInUTC = $nowInUTC->copy()->addMinutes(10);

        $upcomingTodos = Todo::query()
            ->whereNull('deleted_at')
            ->whereHas('user', fn ($q) => $q->whereHas('pushSubscriptions'))
            ->whereBetween('start_at', [$nowInUTC, $toHourInUTC])
            ->with('user')
            ->get();

        Log::info("Upcoming scan: {$upcomingTodos->count()} todos starting between {$now->toDateTimeString()} and ".$toHour->toDateTimeString().'.');

        foreach ($upcomingTodos as $todo) {
            $todo->user->notify(new BrowserPush($todo));
            Log::info("Sent UPCOMING push for Todo ID {$todo->id} to User ID {$todo->user->id}.");
        }

        $ongoingTodos = Todo::query()
            ->whereNull('deleted_at')
            ->whereHas('user', fn ($q) => $q->whereHas('pushSubscriptions'))
            ->where('start_at', '<=', $nowInUTC)
            ->where('end_at', '>=', $nowInUTC)
            ->with('user')
            ->get();

        Log::info("Ongoing scan: {$ongoingTodos->count()} todos ongoing at {$now->toDateTimeString()}.");

        foreach ($ongoingTodos as $todo) {
            $todo->user->notify(new BrowserPush($todo));
            Log::info("Sent ONGOING push for Todo ID {$todo->id} to User ID {$todo->user->id}.");
        }
    }
}
