<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\ScheduleStatus;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(private readonly ScheduleService $schedules) {}

    /** @param array<string, mixed> $data */
    public function record(Booking $booking, array $data, User $processor): Payment
    {
        return DB::transaction(function () use ($booking, $data, $processor): Payment {
            $lockedBooking = Booking::query()->lockForUpdate()->findOrFail($booking->id);

            if (! in_array($lockedBooking->status, [BookingStatus::Pending, BookingStatus::Confirmed], true)) {
                throw ValidationException::withMessages([
                    'booking_id' => 'Pembayaran hanya dapat ditambahkan untuk booking Pending atau Confirmed.',
                ]);
            }

            $amount = round((float) $data['amount'], 2);
            if ($amount !== round((float) $lockedBooking->total_price, 2)) {
                throw ValidationException::withMessages([
                    'amount' => 'Nominal pembayaran harus sama dengan total booking.',
                ]);
            }

            if ($lockedBooking->payments()->where('status', PaymentStatus::Paid->value)->exists()) {
                throw ValidationException::withMessages([
                    'booking_id' => 'Booking ini sudah memiliki pembayaran berhasil.',
                ]);
            }

            $status = PaymentStatus::from($data['status'] ?? PaymentStatus::Pending->value);

            $payment = Payment::query()->create([
                'transaction_number' => $this->generateTransactionNumber(),
                'booking_id' => $lockedBooking->id,
                'processed_by' => $processor->id,
                'method' => $data['method'],
                'amount' => $amount,
                'status' => $status,
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'paid_at' => $status === PaymentStatus::Paid ? now() : null,
            ]);

            if ($status === PaymentStatus::Paid) {
                $lockedBooking->update([
                    'confirmed_at' => $lockedBooking->confirmed_at ?? now(),
                    'status' => BookingStatus::Paid,
                ]);
            }

            return $payment->load('booking.customer');
        }, 3);
    }

    public function changeStatus(Payment $payment, PaymentStatus $target): Payment
    {
        return DB::transaction(function () use ($payment, $target): Payment {
            $locked = Payment::query()->lockForUpdate()->findOrFail($payment->id);
            $current = $locked->status;

            if ($current === $target) {
                return $locked;
            }

            if ($target === PaymentStatus::Paid && ! in_array($current, [PaymentStatus::Pending, PaymentStatus::Failed], true)) {
                throw ValidationException::withMessages(['status' => 'Status pembayaran tidak valid.']);
            }

            if ($target === PaymentStatus::Refunded && $current !== PaymentStatus::Paid) {
                throw ValidationException::withMessages(['status' => 'Hanya pembayaran Paid yang dapat direfund.']);
            }

            if ($target === PaymentStatus::Failed && $current !== PaymentStatus::Pending) {
                throw ValidationException::withMessages(['status' => 'Hanya pembayaran Pending yang dapat ditandai Failed.']);
            }

            $locked->update([
                'status' => $target,
                'paid_at' => $target === PaymentStatus::Paid ? ($locked->paid_at ?? now()) : $locked->paid_at,
            ]);

            $booking = Booking::query()->lockForUpdate()->findOrFail($locked->booking_id);

            if ($target === PaymentStatus::Paid) {
                if (round((float) $locked->amount, 2) !== round((float) $booking->total_price, 2)) {
                    throw ValidationException::withMessages(['amount' => 'Nominal tidak sesuai total booking.']);
                }

                if ($booking->payments()->where('id', '!=', $locked->id)->where('status', PaymentStatus::Paid->value)->exists()) {
                    throw ValidationException::withMessages(['booking_id' => 'Booking sudah dibayar.']);
                }

                $booking->update([
                    'confirmed_at' => $booking->confirmed_at ?? now(),
                    'status' => BookingStatus::Paid,
                ]);
            }

            if ($target === PaymentStatus::Refunded && $booking->status->canTransitionTo(BookingStatus::Cancelled)) {
                $booking->update([
                    'status' => BookingStatus::Cancelled,
                    'cancelled_at' => now(),
                    'cancellation_reason' => 'Pembayaran direfund',
                ]);
                $booking->schedule()->update(['status' => ScheduleStatus::Released]);
                $this->schedules->syncUnit($booking->unit);
            }

            return $locked->fresh('booking');
        }, 3);
    }

    private function generateTransactionNumber(): string
    {
        do {
            $number = 'PAY-'.now()->format('Ymd').'-'.Str::upper(Str::random(7));
        } while (Payment::query()->where('transaction_number', $number)->exists());

        return $number;
    }
}
