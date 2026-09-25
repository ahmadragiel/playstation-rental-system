<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('q', $request->input('search', '')));
        $bookingFilter = (string) $request->input('bookings', '');

        $query = Customer::query()
            ->withCount('bookings')
            ->withSum([
                'bookings as total_transactions' => function (Builder $query): void {
                    $query->whereHas('payments', fn (Builder $payment) => $payment->where('status', PaymentStatus::Paid->value));
                },
            ], 'total_price')
            ->withMax('bookings', 'start_at');

        $query->when($search !== '', function (Builder $query) use ($search): void {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('whatsapp', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        });

        if ($bookingFilter === 'with') {
            $query->has('bookings');
        } elseif ($bookingFilter === 'without') {
            $query->doesntHave('bookings');
        }

        $customers = $query
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
        ]);
    }

    public function show(Customer $customer): View
    {
        $bookingRelations = ['detail', 'unit', 'package', 'payments'];

        $totalBookings = $customer->bookings()->count();
        $bookings = $customer->bookings()
            ->with($bookingRelations)
            ->latest('created_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $latestBooking = $customer->bookings()
            ->with($bookingRelations)
            ->latest('created_at')
            ->latest('id')
            ->first();

        $paidPayments = Payment::query()
            ->where('status', PaymentStatus::Paid->value)
            ->whereHas('booking', function (Builder $query) use ($customer): void {
                $query->where('customer_id', $customer->getKey());
            });

        $paidTransactionsCount = (clone $paidPayments)->count();
        $totalPaidAmount = (float) (clone $paidPayments)->sum('amount');

        return view('admin.customers.show', [
            'customer' => $customer,
            'bookings' => $bookings,
            'latestBooking' => $latestBooking,
            'totalBookings' => $totalBookings,
            'paidTransactionsCount' => $paidTransactionsCount,
            'totalPaidAmount' => $totalPaidAmount,
            'totalPaid' => $totalPaidAmount,
        ]);
    }
}
