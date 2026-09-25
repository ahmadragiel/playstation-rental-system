<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PaymentStatusRequest;
use App\Http\Requests\Admin\StorePaymentRequest;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $payments) {}

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::enum(PaymentStatus::class)],
            'method' => ['nullable', 'string', 'max:20'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
        ]);

        $payments = Payment::query()
            ->with(['booking:id,public_id,booking_number,total_price,customer_id', 'booking.customer:id,name,whatsapp', 'processor:id,name'])
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('transaction_number', 'like', "%{$search}%")
                        ->orWhereHas('booking', fn ($booking) => $booking
                            ->where('booking_number', 'like', "%{$search}%")
                            ->orWhereHas('customer', fn ($customer) => $customer->where('name', 'like', "%{$search}%")));
                });
            })
            ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filters['method'] ?? null, fn ($query, $method) => $query->where('method', $method))
            ->when($filters['date_from'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, $date) => $query->whereDate('created_at', '<=', $date))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.payments.index', [
            'payments' => $payments,
            'filters' => $filters,
            'statuses' => PaymentStatus::cases(),
            'bookings' => Booking::query()
                ->with('customer:id,name')
                ->whereIn('status', ['pending', 'confirmed'])
                ->latest('start_at')
                ->limit(100)
                ->get(),
        ]);
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $booking = Booking::query()->findOrFail($data['booking_id']);
        $this->payments->record($booking, $data, $request->user());

        return back()->with('success', 'Transaksi pembayaran berhasil dicatat.');
    }

    public function updateStatus(PaymentStatusRequest $request, Payment $payment): RedirectResponse
    {
        $this->payments->changeStatus($payment, PaymentStatus::from($request->validated('status')));

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}
