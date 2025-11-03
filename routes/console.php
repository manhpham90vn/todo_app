<?php

use App\Jobs\SendWebPush;
use App\Jobs\SendWebPush2;
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
    ->runInBackground();

Schedule::command('app:calendar-sync-command')
    ->hourlyAt(15)
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();

Schedule::command('app:todo-sync-command')
    ->hourlyAt(30)
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();

Schedule::job(UpdateTodoStatus::class)
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::job(SendWebPush::class)
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::job(SendWebPush2::class)
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->onOneServer();
