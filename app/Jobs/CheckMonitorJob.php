<?php

namespace App\Jobs;

use App\Enums\MonitorStatusEnum;
use App\Models\Monitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\SiteDownMail;
use App\Mail\SiteRecoveredMail;

class CheckMonitorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Monitor $monitor) {}

    public function handle(): void
    {
        $start = now();
        $isUp = false;
        $statusCode = 0;
        $responseTime = null;

        try {
            $response = Http::timeout(10)->get($this->monitor->url);
            $responseTime = now()->diffInMilliseconds($start);
            $statusCode = $response->status();
            $isUp = $statusCode < 400;
        } catch (\Exception $e) {}

        $this->monitor->checks()->create([
            'status_code'      => $statusCode,
            'response_time_ms' => $responseTime,
            'is_up'            => $isUp,
            'checked_at'       => now(),
        ]);

        $previousStatus = $this->monitor->status;
        $failures = $isUp ? 0 : $this->monitor->consecutive_failures + 1;

        $newStatus = match(true) {
            $isUp => MonitorStatusEnum::UP,
            $failures >= $this->monitor->threshold => MonitorStatusEnum::DOWN,
            default => $previousStatus === MonitorStatusEnum::DOWN ? MonitorStatusEnum::DOWN : MonitorStatusEnum::PENDING,
        };

        $this->monitor->update([
            'status'               => $newStatus,
            'consecutive_failures' => $failures,
            'last_checked_at'      => now(),
            'next_check_at'        => now()->addMinutes($this->monitor->check_interval),
        ]);

        if ($newStatus === MonitorStatusEnum::DOWN && $previousStatus !== MonitorStatusEnum::DOWN) {
            Mail::to(config('uptime.notify_email'))->send(new SiteDownMail($this->monitor));
        }

        if ($isUp && $previousStatus === MonitorStatusEnum::DOWN) {
            Mail::to(config('uptime.notify_email'))->send(new SiteRecoveredMail($this->monitor));
        }
    }
}