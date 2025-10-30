<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanFailedJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-failed-jobs';

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
        $count = DB::table('failed_jobs')
            ->where('failed_at', '<=', now()->subDays(7))
            ->delete();
        $this->info("Deleted {$count} failed jobs older than 7 days.");

        return 0;
    }
}
