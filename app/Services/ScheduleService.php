<?php

namespace App\Services;

use App\Enums\ScheduleStatus;
use App\Enums\UnitStatus;
use App\Models\PlaystationUnit;
use App\Models\Schedule;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;

class ScheduleService
{
    /** @return Collection<int, Schedule> */
    public function schedulesForDate(CarbonInterface $date, ?int $unitId = null): Collection
    {
        $query = Schedule::query()
            ->whereIn('status', [ScheduleStatus::Reserved->value, ScheduleStatus::InProgress->value])
            ->where('start_at', '<', $date->copy()->addDay()->startOfDay())
            ->where('end_at', '>', $date->copy()->startOfDay())
            ->with([
                'booking:id,public_id,booking_number,status,start_at,end_at,customer_id',
                'booking.customer:id,name,whatsapp',
                'unit:id,code,name,playstation_type_id',
            ]);

        if ($unitId) {
            $query->where('playstation_unit_id', $unitId);
        }

        return $query->orderBy('start_at')->get();
    }

    public function syncUnit(PlaystationUnit $unit): PlaystationUnit
    {
        if (! $unit->is_active || $unit->status === UnitStatus::Maintenance) {
            return $unit;
        }

        $now = now();
        $current = $unit->schedules()
            ->whereIn('status', [ScheduleStatus::Reserved->value, ScheduleStatus::InProgress->value])
            ->where('start_at', '<=', $now)
            ->where('end_at', '>', $now)
            ->orderByRaw("CASE WHEN status = 'in_progress' THEN 0 ELSE 1 END")
            ->first();

        if ($current) {
            $newStatus = $current->status === ScheduleStatus::InProgress
                ? UnitStatus::InUse
                : UnitStatus::Booked;
        } else {
            $futureExists = $unit->schedules()
                ->whereIn('status', [ScheduleStatus::Reserved->value, ScheduleStatus::InProgress->value])
                ->where('start_at', '>', $now)
                ->exists();

            $newStatus = $futureExists ? UnitStatus::Booked : UnitStatus::Available;
        }

        if ($unit->status !== $newStatus) {
            $unit->update(['status' => $newStatus]);
        }

        return $unit;
    }

    public function syncAll(): void
    {
        PlaystationUnit::query()
            ->where('is_active', true)
            ->with('schedules')
            ->each(fn (PlaystationUnit $unit) => $this->syncUnit($unit));
    }
}
