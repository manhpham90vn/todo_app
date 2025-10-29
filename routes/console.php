<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:clean-failed-jobs')
    ->dailyAt('00:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/clean_failed_jobs.log'));

Schedule::command('app:calendar-sync-command')
    ->dailyAt('00:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/calendar_sync.log'));

Schedule::command('app:todo-sync-command')
    ->dailyAt('01:00')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/app_todo_sync.log'));
