<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncGoogleCalendarAllUsersJob implements ShouldQueue
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
        User::query()
            ->with([
                'googleTokens' => fn ($q) => $q->latest('created_at')->limit(1),
            ])
            ->orderBy('id')
            ->chunkById(200, function ($users) {
                foreach ($users as $user) {
                    Log::info("Processing user: {$user->id} - {$user->name}");

                    $token = $user->googleTokens->first();
                    if (! $token) {
                        continue;
                    }

                    dispatch(new SyncGoogleCalendarJob($user->id, $token));
                }
            });
    }
}
