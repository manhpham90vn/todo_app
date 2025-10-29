<?php

namespace App\Console\Commands;

use App\Models\CalendarEvent;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Console\Command;

class TodoSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:todo-sync-command {user_id?}';

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
        $userId = $this->argument('user_id');

        if ($userId) {
            $user = User::find($userId);
            $this->process($user);
        } else {
            $users = User::all();
            foreach ($users as $user) {
                $this->process($user);

            }
        }

    }

    public function process(User $user): void
    {
        $this->info("Processing user: {$user->id} - {$user->name}");

        $events = CalendarEvent::where('user_id', $user->id)->get();
        foreach ($events as $event) {
            $priority = 'medium';
            if ($event->summary && preg_match('/\b(deploy|release)\b/i', $event->summary)) {
                $priority = 'high';
            }
            Todo::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'external_id' => $event->google_event_id],
                [
                    'title' => $event->summary,
                    'description' => $event->description,
                    'priority' => $priority,
                    'created_at' => $event->start_at,
                ]);
        }
    }
}
