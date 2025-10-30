<?php

namespace App\Jobs;

use App\Models\Todo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateTodoStatus implements ShouldQueue
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
        $now = Carbon::now();

        Log::info("Updating todos status for {$now->toDateTimeString()}");

        Todo::where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->whereNotIn('status', ['doing', 'completed'])
            ->update(['status' => 'doing']);
    }
}
