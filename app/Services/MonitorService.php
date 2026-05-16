<?php

namespace App\Services;

use App\Enums\MonitorStatusEnum;
use App\Models\Monitor;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MonitorService
{
    public function getMonitors()
    {
        $monitors = Monitor::orderBy('id', 'desc')->get();

        return $monitors;
    }

    public function createMonitor(array $data): Monitor
    {
        $monitor = Monitor::create([
            'url' => $data['url'],
            'check_interval' => $data['check_interval'],
            'threshold' => $data['threshold'],
            'status' => MonitorStatusEnum::PENDING,
        ]);
        
        return $monitor;
    }

    public function getMonitorHistory(int $monitorId)
    {
        $monitor = Monitor::find($monitorId);
        if (!$monitor) {
            throw new ModelNotFoundException('Monitor not found');
        }
        
        $checks = $monitor->checks()->orderBy('checked_at', 'desc')->paginate(15);
        
        return $checks;
    }
}