<?php

namespace App\Jobs;

use App\Models\Todo;
use App\Notifications\BrowserPush;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendWebPush2 implements ShouldQueue
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
        $nowInUTC = now()->utc();

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
