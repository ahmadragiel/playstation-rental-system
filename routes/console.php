<?php

use App\Services\ScheduleService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('rental:sync-unit-statuses', function () {
    $this->laravel->make(ScheduleService::class)->syncAll();
    $this->info('Status unit berhasil disinkronkan.');
})->purpose('Sinkronkan status unit berdasarkan jadwal aktif');

Schedule::command('rental:sync-unit-statuses')
    ->everyMinute()
    ->withoutOverlapping();
