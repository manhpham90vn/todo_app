<?php

namespace App\Console\Commands;

use App\Jobs\SyncGoogleCalendarJob;
use App\Models\User;
use Illuminate\Console\Command;

class CalendarSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calendar-sync-command {user_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = 'primary';
        $userId = $this->argument('user_id');

        if ($userId) {
            $user = User::with([
                'googleTokens' => fn ($q) => $q->latest('created_at')->limit(1),
            ])->find($userId);
            $this->info("Processing user: {$user->id} - {$user->name}");

            $token = $user->googleTokens->first();
            if (! $token) {
                $this->info("No Google tokens found for user: {$user->id} - {$user->name}");

                return 0;
            }

            dispatch(new SyncGoogleCalendarJob($id, $token, $user->id));
        } else {
            User::query()
                ->with([
                    'googleTokens' => fn ($q) => $q->latest('created_at')->limit(1),
                ])
                ->orderBy('id')
                ->chunkById(200, function ($users) use ($id) {
                    foreach ($users as $user) {
                        $this->info("Processing user: {$user->id} - {$user->name}");

                        $token = $user->googleTokens->first();
                        if (! $token) {
                            $this->info("No Google tokens found for user: {$user->id} - {$user->name}");

                            continue;
                        }

                        dispatch(new SyncGoogleCalendarJob($id, $token, $user->id));
                    }
                });
        }

        return 0;
    }
}
