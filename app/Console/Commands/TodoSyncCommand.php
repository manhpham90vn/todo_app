<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TodoSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:todo-sync-command';

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
        $users = User::all();
        foreach ($users as $user) {
            $this->info("Processing user: {$user->id} - {$user->name}");
            $tokens = DB::table('google_tokens')
                ->where('user_id', $user->id)
                ->latest('created_at')
                ->first();
            if (! $tokens) {
                $this->info("No Google tokens found for user: {$user->id} - {$user->name}");
                break;
            }

        }
        $events = DB::table('calendar_events')->get();

        foreach ($events as $event) {

            $this->info("Processing event: {$event->summary}");

            $exists = DB::table('todos')
                ->where('title', $event->summary)
                ->where('description', $event->description)
                ->where('created_at', $event->start_at)
                ->exists();

            $this->info("Exists: {$exists} event: {$event->summary}");

            if (! $exists) {
                DB::table('todos')->insert([
                    'user_id' => 1,
                    'title' => $event->summary ?? 'No Title',
                    'description' => $event->description,
                    'is_complete' => false,
                    'priority' => 'medium',
                    'created_at' => $event->start_at,
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
