<?php

namespace App\Models;

use App\Enums\MonitorStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitor extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'check_interval' => 'integer',
        'threshold' => 'integer',
        'consecutive_failures' => 'integer',
        'last_checked_at' => 'datetime',
        'status' => MonitorStatusEnum::class,
    ];

    protected $appends = ['uptime_percentage'];

    public function checks()
    {
        return $this->hasMany(MonitorCheck::class);
    }

    public function getUptimePercentageAttribute(): ?float
    {
        $total = $this->checks()->count();
        if ($total === 0) return null;
        $up = $this->checks()->where('is_up', true)->count();
        return round(($up / $total) * 100, 2);
    }
}
