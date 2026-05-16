<?php

use App\Jobs\CheckMonitorJob;
use App\Models\Monitor;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    Monitor::where('next_check_at', '<=', now())
        ->orWhereNull('next_check_at')
        ->each(fn ($monitor) => CheckMonitorJob::dispatch($monitor));
})->everyMinute();
