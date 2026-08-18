<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Daily cleanup: soft-deleted records older than 30 days
Schedule::command('model:prune')->dailyAt('02:00');

// TODO: Implement backup schedule via BackupService
// Currently backups are handled manually via the admin interface.

// Hourly: generate seller revenue reports
Schedule::call(function () {
    Artisan::call('app:generate-daily-reports');
})->hourlyAt(15);

// Weekly: prune Telescope entries older than 7 days
Schedule::command('telescope:prune --hours=168')->weekly();

// Daily: clean expired password reset tokens
Schedule::command('auth:clear-resets')->daily();

// Every 5 minutes: run queue worker if not running
Schedule::command('queue:work --stop-when-empty --max-time=300')->everyFiveMinutes()->withoutOverlapping();

// Auto-cancel đơn đề xuất giá khi người mua không xác nhận trong 12h
Schedule::command('offers:cancel-expired')->everyMinute()->withoutOverlapping();
