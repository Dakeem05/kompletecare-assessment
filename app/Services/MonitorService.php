<?php

namespace App\Services;

use App\Models\Monitor;

class MonitorService
{
    public function getMonitors()
    {
        $monitors = Monitor::orderBy('id', 'desc')->get();

        return $monitors;
    }
}