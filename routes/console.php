<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

//Schedule::command('app:prune-inactive')->everyFiveMinutes();
//Schedule::command('app:plan-status')->everyMinute();
//Schedule::command('app:beneficiary-status')->everyMinute();
