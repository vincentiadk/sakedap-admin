<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

$scheduleLog = fn(string $cmd, string $event) => Log::channel('schedule')->info("[$event] $cmd");

Schedule::command('app:release-review-lock')
    ->dailyAt('23:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->evenInMaintenanceMode()
    ->before(fn() => $scheduleLog('app:release-review-lock', 'START'))
    ->after(fn() => $scheduleLog('app:release-review-lock', 'DONE'));

Schedule::command('app:sync-isbn-received-date')
    ->hourly()
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->evenInMaintenanceMode()
    ->before(fn() => $scheduleLog('app:sync-isbn-received-date', 'START'))
    ->after(fn() => $scheduleLog('app:sync-isbn-received-date', 'DONE'));

Schedule::command('app:auto-block-publisher-kckr')
    ->dailyAt('00:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->evenInMaintenanceMode()
    ->before(fn() => $scheduleLog('app:auto-block-publisher-kckr', 'START'))
    ->after(fn() => $scheduleLog('app:auto-block-publisher-kckr', 'DONE'));

Schedule::command('app:auto-grant-command')
    ->dailyAt('03:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->evenInMaintenanceMode()
    ->before(fn() => $scheduleLog('app:auto-grant-command', 'START'))
    ->after(fn() => $scheduleLog('app:auto-grant-command', 'DONE'));

Schedule::command('app:ro-update-status')
    ->everyThirtyMinutes()
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->evenInMaintenanceMode()
    ->before(fn() => $scheduleLog('app:ro-update-status', 'START'))
    ->after(fn() => $scheduleLog('app:ro-update-status', 'DONE'));

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
