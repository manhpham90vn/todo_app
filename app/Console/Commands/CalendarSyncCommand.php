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
            $user = User::find($userId);
            $token = $user->googleTokens()->latest('created_at')->first();
            if (! $token) {
                $this->info("No Google tokens found for user: {$user->id} - {$user->name}");

                return 0;
            }

            $this->info("Found Google tokens for user: {$user->id} - {$user->name}");
            dispatch(new SyncGoogleCalendarJob($id, $token, $user->id));
        } else {
            $users = User::all();
            foreach ($users as $user) {
                $this->info("Processing user: {$user->id} - {$user->name}");
                $token = $user->googleTokens()->latest('created_at')->first();
                if (! $token) {
                    $this->info("No Google tokens found for user: {$user->id} - {$user->name}");
                    break;
                }

                $this->info("Found Google tokens for user: {$user->id} - {$user->name}");
                dispatch(new SyncGoogleCalendarJob($id, $token, $user->id));
            }
        }

        return 0;
    }
}
