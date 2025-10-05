<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:auto-grant-command')
    ->dailyAt('03:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->evenInMaintenanceMode();
