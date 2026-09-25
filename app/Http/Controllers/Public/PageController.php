<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Package;
use App\Models\PlaystationUnit;
use App\Services\SettingService;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function __construct(private readonly SettingService $settings) {}

    public function home(): View
    {
        return view('public.home', [
            'settings' => $this->settings->all(),
            'packages' => Package::query()->active()->with('type')->orderByDesc('is_featured')->latest()->take(6)->get(),
            'units' => PlaystationUnit::query()
                ->with([
                    'type',
                    'games:id,name',
                    'schedules' => fn ($query) => $query->whereIn('status', ['reserved', 'in_progress'])
                        ->where('start_at', '<=', now())->where('end_at', '>', now()),
                ])
                ->latest()->take(8)->get(),
            'games' => Game::query()->active()->with('units:id,code')->latest()->take(8)->get(),
            'availableUnitCount' => PlaystationUnit::query()
                ->where('is_active', true)
                ->where('status', '!=', 'maintenance')
                ->whereDoesntHave('schedules', fn ($query) => $query
                    ->whereIn('status', ['reserved', 'in_progress'])
                    ->where('start_at', '<=', now())
                    ->where('end_at', '>', now()))
                ->count(),
        ]);
    }

    public function packages(): View
    {
        return view('public.packages', [
            'settings' => $this->settings->all(),
            'packages' => Package::query()->active()->with('type')->orderByDesc('is_featured')->orderBy('price')->get(),
        ]);
    }

    public function units(): View
    {
        return view('public.units', [
            'settings' => $this->settings->all(),
            'units' => PlaystationUnit::query()
                ->with([
                    'type',
                    'games:id,name',
                    'schedules' => fn ($query) => $query->whereIn('status', ['reserved', 'in_progress'])
                        ->where('start_at', '<=', now())->where('end_at', '>', now()),
                ])
                ->orderBy('code')->get(),
        ]);
    }

    public function games(): View
    {
        return view('public.games', [
            'settings' => $this->settings->all(),
            'games' => Game::query()->active()->with('units:id,code')->orderBy('name')->get(),
        ]);
    }

    public function facilities(): View
    {
        return view('public.facilities', ['settings' => $this->settings->all()]);
    }

    public function howItWorks(): View
    {
        return view('public.how-it-works', ['settings' => $this->settings->all()]);
    }

    public function contact(): View
    {
        return view('public.contact', ['settings' => $this->settings->all()]);
    }
}
