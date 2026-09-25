<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Package;
use App\Models\PlaystationUnit;
use App\Services\BookingService;
use App\Services\SettingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class PublicBookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
        private readonly SettingService $settings,
    ) {}

    public function create(): View
    {
        return view('public.booking.create', [
            'settings' => $this->settings->all(),
            'packages' => Package::query()->active()->with('type')->orderBy('price')->get(),
            'units' => PlaystationUnit::query()->with(['type', 'games:id,name'])->where('is_active', true)->orderBy('code')->get(),
        ]);
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $booking = $this->bookingService->create($request->validated(), $request->user()?->id);
        $request->session()->put('latest_booking_public_id', $booking->public_id);

        return redirect()
            ->route('booking.success', $booking)
            ->with('success', 'Booking berhasil dibuat. Simpan nomor booking Anda.');
    }

    public function success(Booking $booking): View
    {
        abort_unless(session('latest_booking_public_id') === $booking->public_id, 403);

        return view('public.booking.success', [
            'settings' => $this->settings->all(),
            'booking' => $booking->load(['customer', 'package', 'unit.type', 'detail', 'payments']),
        ]);
    }

    public function quote(): JsonResponse
    {
        $data = $this->validateQuoteRequest();

        return response()->json($this->bookingService->quote($data));
    }

    public function availability(): JsonResponse
    {
        $data = $this->validateQuoteRequest();
        $units = $this->bookingService->availableUnits($data);

        return response()->json([
            'data' => $units->map(fn (PlaystationUnit $unit): array => [
                'id' => $unit->id,
                'code' => $unit->code,
                'name' => $unit->name,
                'type' => $unit->type->name,
                'status' => $unit->effectiveStatus()->value,
                'status_label' => $unit->effectiveStatus()->label(),
                'games' => $unit->games->pluck('name')->values(),
            ])->values(),
            'meta' => [
                'available' => $units->count(),
                'checked_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /** @return array<string, mixed> */
    private function validateQuoteRequest(): array
    {
        return request()->validate([
            'package_id' => ['required', 'integer', 'exists:packages,id'],
            'booking_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
        ]);
    }
}
