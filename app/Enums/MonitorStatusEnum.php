<?php

namespace App\Enums;

enum MonitorStatusEnum: string
{
    case PENDING = 'pending';
    case UP = 'up';
    case DOWN = 'down';

    public static function toArray(): array
    {
        return array_column(MonitorStatusEnum::cases(), 'value');
    }
}
