<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Validation\ValidationException;

class OperatingHoursService
{
    public function __construct(private readonly SettingService $settings) {}

    public function assertWithin(CarbonInterface $startAt, CarbonInterface $endAt): void
    {
        if (! $this->isWithin($startAt, $endAt)) {
            throw ValidationException::withMessages([
                'start_time' => 'Waktu yang dipilih berada di luar jam operasional rental.',
            ]);
        }
    }

    public function isWithin(CarbonInterface $startAt, CarbonInterface $endAt): bool
    {
        if ($endAt->lessThanOrEqualTo($startAt)) {
            return false;
        }

        $candidateWindows = [
            $this->windowFor($startAt->toImmutable()),
            $this->windowFor($startAt->toImmutable()->subDay()),
        ];

        foreach ($candidateWindows as [$opensAt, $closesAt]) {
            if ($startAt->greaterThanOrEqualTo($opensAt) && $endAt->lessThanOrEqualTo($closesAt)) {
                return true;
            }
        }

        return false;
    }

    /** @return array{0: CarbonImmutable, 1: CarbonImmutable} */
    private function windowFor(CarbonImmutable $date): array
    {
        $hours = trim((string) $this->settings->get('opening_hours', '10:00-02:00'));
        [$opening, $closing] = array_pad(explode('-', $hours, 2), 2, '02:00');

        $opensAt = $date->setTimeFromTimeString(trim($opening));
        $closesAt = $date->addDay()->setTimeFromTimeString(trim($closing));

        if ($closesAt->lessThanOrEqualTo($opensAt)) {
            $closesAt = $closesAt->addDay();
        }

        return [$opensAt, $closesAt];
    }
}
