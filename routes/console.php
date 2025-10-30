<?php

use App\Jobs\UpdateTodoStatus;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:clean-failed-jobs')
    ->hourlyAt(0)
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/clean_failed_jobs.log'));

Schedule::command('app:calendar-sync-command')
    ->hourlyAt(15)
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/calendar_sync.log'));

Schedule::command('app:todo-sync-command')
    ->hourlyAt(30)
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/app_todo_sync.log'));

Schedule::job(UpdateTodoStatus::class)
    ->everyMinute()
    ->withoutOverlapping()
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/update_todo_status.log'));
