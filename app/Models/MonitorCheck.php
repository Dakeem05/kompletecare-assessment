<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitorCheck extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status_code' => 'integer',
        'response_time_ms' => 'integer',
        'is_up' => 'boolean',
        'checked_at' => 'datetime',
    ];

    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }
}
