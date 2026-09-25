<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlaystationUnit;
use App\Services\ScheduleService;
use App\Services\SettingService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function __construct(
        private readonly ScheduleService $schedules,
        private readonly SettingService $settings,
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:'.now()->addYear()->toDateString()],
            'unit_id' => ['nullable', 'integer', 'exists:playstation_units,id'],
        ]);

        $date = CarbonImmutable::createFromFormat('Y-m-d', $filters['date'] ?? now()->toDateString())->startOfDay();
        $schedules = $this->schedules->schedulesForDate($date, $filters['unit_id'] ?? null);

        $units = PlaystationUnit::query()
            ->with(['type', 'games:id,name'])
            ->when($filters['unit_id'] ?? null, fn ($query, $unitId) => $query->whereKey($unitId))
            ->orderBy('code')
            ->get();

        $units->each(function (PlaystationUnit $unit) use ($schedules): void {
            $unitSchedules = $schedules->where('playstation_unit_id', $unit->id)->values();
            $unit->setRelation('day_schedules', $unitSchedules);
            $unit->setRelation('schedules', $unitSchedules);
        });

        return view('admin.schedule', [
            'settings' => $this->settings->all(),
            'date' => $date,
            'units' => $units,
            'allUnits' => PlaystationUnit::query()->orderBy('code')->get(['id', 'code', 'name']),
            'filters' => $filters,
        ]);
    }
}
