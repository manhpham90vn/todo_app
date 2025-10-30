<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SyncTodoJobAllUsersJob implements ShouldQueue
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
        User::select('id', 'name')
            ->with(['events' => function ($q) {
                $q->select('id', 'user_id', 'google_event_id', 'summary', 'description', 'start_at', 'end_at');
            }])
            ->orderBy('id')
            ->chunkById(200, function ($users) {
                foreach ($users as $user) {
                    dispatch(new SyncTodoJob($user->id, $user->events));
                }
            });
    }
}
