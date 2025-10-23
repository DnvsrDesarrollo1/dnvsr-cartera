<?php

use Illuminate\Support\Facades\Schedule;

//Schedule::command('app:prune-inactive')->everyFiveMinutes();
Schedule::command('app:plan-status')->everyFiveMinutes();
Schedule::command('app:beneficiary-status')->everyFiveMinutes();
