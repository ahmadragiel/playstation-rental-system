<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PlaystationUnit;
use App\Services\ScheduleService;
use App\Services\SettingService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PublicScheduleController extends Controller
{
    public function __construct(
        private readonly ScheduleService $schedules,
        private readonly SettingService $settings,
    ) {}

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:today', 'before_or_equal:'.now()->addDays(90)->toDateString()],
        ]);

        $date = CarbonImmutable::createFromFormat('Y-m-d', $validated['date'] ?? now()->toDateString())->startOfDay();
        $schedules = $this->schedules->schedulesForDate($date);
        $units = PlaystationUnit::query()
            ->with(['type', 'games:id,name'])
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $units->each(function (PlaystationUnit $unit) use ($schedules): void {
            $unitSchedules = $schedules->where('playstation_unit_id', $unit->id)->values();

            $unitSchedules->each(function ($schedule): void {
                $schedule->setRelation('booking', null);
                $schedule->setRelation('unit', null);
            });

            $unit->setRelation('day_schedules', $unitSchedules);
            $unit->setRelation('schedules', $unitSchedules);
        });

        return view('public.schedule', [
            'settings' => $this->settings->all(),
            'date' => $date,
            'units' => $units,
        ]);
    }
}
