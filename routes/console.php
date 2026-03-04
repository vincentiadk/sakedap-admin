<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:auto-grant-command')
    ->dailyAt('03:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->evenInMaintenanceMode();

Schedule::command('app:ro-update-status')
    ->everyThirtyMinutes()
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->evenInMaintenanceMode();

Schedule::exec('find ' . storage_path('logs') . ' -name "laravel-*.log" -mtime +7 -delete')
    ->weekly();

Schedule::exec('find ' . storage_path('logs') . ' -name "raja-ongkir-api-*.log" -mtime +7 -delete')
    ->weekly();

Schedule::exec('find ' . storage_path('logs') . ' -name "isbn-api-*.log" -mtime +7 -delete')
    ->weekly();

Schedule::exec('find ' . storage_path('logs') . ' -name "sakedap-api-*.log" -mtime +7 -delete')
    ->weekly();

Schedule::exec('find ' . storage_path('logs') . ' -name "report-*.log" -mtime +7 -delete')
    ->weekly();
