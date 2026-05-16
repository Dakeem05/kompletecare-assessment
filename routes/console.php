<?php

use App\Jobs\CheckMonitor;
use App\Models\Monitor;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    Monitor::where('next_check_at', '<=', now())
        ->orWhereNull('next_check_at')
        ->each(fn ($monitor) => CheckMonitor::dispatch($monitor));
})->everyMinute();
