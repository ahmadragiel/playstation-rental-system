<?php

namespace App\Enums;

enum ScheduleStatus: string
{
    case Reserved = 'reserved';
    case InProgress = 'in_progress';
    case Released = 'released';

    public function label(): string
    {
        return match ($this) {
            self::Reserved => 'Booked',
            self::InProgress => 'In Use',
            self::Released => 'Available',
        };
    }
}
