<?php

namespace App\Enums;

enum UnitStatus: string
{
    case Available = 'available';
    case Booked = 'booked';
    case InUse = 'in_use';
    case Maintenance = 'maintenance';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Booked => 'Booked',
            self::InUse => 'In Use',
            self::Maintenance => 'Maintenance',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Available => 'emerald',
            self::Booked => 'blue',
            self::InUse => 'cyan',
            self::Maintenance => 'amber',
        };
    }
}
