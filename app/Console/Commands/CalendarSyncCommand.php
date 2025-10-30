<?php

namespace App\Console\Commands;

use App\Jobs\SyncGoogleCalendarAllUsersJob;
use Illuminate\Console\Command;

class CalendarSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:calendar-sync-command';

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
        dispatch(new SyncGoogleCalendarAllUsersJob);

        return 0;
    }
}
