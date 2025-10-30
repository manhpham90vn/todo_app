<?php

namespace App\Console\Commands;

use App\Jobs\SyncTodoJobAllUsersJob;
use Illuminate\Console\Command;

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
        dispatch(new SyncTodoJobAllUsersJob);

        return 0;
    }
}
