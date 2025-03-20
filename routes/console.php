<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    \App\Models\Plan::where('estado', 'INACTIVO')->delete();
    \App\Models\Helper::where('estado', 'INACTIVO')->delete();
    \App\Models\Readjustment::where('estado', 'INACTIVO')->delete();
})->hourly();
