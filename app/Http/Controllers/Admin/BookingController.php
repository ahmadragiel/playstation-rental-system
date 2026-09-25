<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BookingStatusRequest;
use App\Models\Booking;
use App\Services\BookingStatusService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function __construct(private readonly BookingStatusService $statusService) {}

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::enum(BookingStatus::class)],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
        ]);

        $bookings = Booking::query()
            ->with(['customer:id,name,whatsapp,email', 'package:id,name', 'unit:id,code,name,playstation_type_id', 'payments:id,booking_id,status,amount,method'])
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('booking_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($customer) => $customer
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('whatsapp', 'like', "%{$search}%"));
                });
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('start_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('start_at', '<=', $date))
            ->latest('start_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'filters' => $filters,
            'statuses' => BookingStatus::cases(),
        ]);
    }

    public function show(Booking $booking): View
    {
        return view('admin.bookings.show', [
            'booking' => $booking->load([
                'customer',
                'package',
                'unit.type',
                'detail',
                'payments.processor',
                'schedule',
                'creator',
            ]),
            'statuses' => $booking->status->allowedTransitions(),
        ]);
    }

    public function updateStatus(BookingStatusRequest $request, Booking $booking): RedirectResponse
    {
        $this->statusService->transition(
            $booking,
            BookingStatus::from($request->validated('status')),
            $request->validated('reason'),
        );

        return back()->with('success', 'Status booking berhasil diperbarui.');
    }
}
