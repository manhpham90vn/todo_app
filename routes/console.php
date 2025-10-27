<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:clean-failed-jobs')
    ->dailyAt('00:30')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();
