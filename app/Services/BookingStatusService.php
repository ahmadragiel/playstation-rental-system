<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\ScheduleStatus;
use App\Enums\UnitStatus;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingStatusService
{
    public function __construct(private readonly ScheduleService $schedules) {}

    public function transition(Booking $booking, BookingStatus $target, ?string $reason = null): Booking
    {
        return DB::transaction(function () use ($booking, $target, $reason): Booking {
            $locked = Booking::query()->lockForUpdate()->findOrFail($booking->id);
            $current = $locked->status;

            if (! $current->canTransitionTo($target)) {
                throw ValidationException::withMessages([
                    'status' => "Status {$current->label()} tidak dapat diubah menjadi {$target->label()}.",
                ]);
            }

            if ($target === BookingStatus::Paid) {
                throw ValidationException::withMessages([
                    'status' => 'Gunakan konfirmasi pembayaran agar nominal divalidasi.',
                ]);
            }

            $updates = match ($target) {
                BookingStatus::Confirmed => [
                    'status' => $target,
                    'confirmed_at' => now(),
                ],
                BookingStatus::Ongoing => [
                    'status' => $target,
                    'started_at' => now(),
                ],
                BookingStatus::Completed => [
                    'status' => $target,
                    'completed_at' => now(),
                ],
                BookingStatus::Cancelled => [
                    'status' => $target,
                    'cancelled_at' => now(),
                    'cancellation_reason' => $reason ?: 'Dibatalkan oleh admin',
                ],
                default => ['status' => $target],
            };

            $locked->update($updates);

            if ($target === BookingStatus::Ongoing) {
                $locked->schedule()->update(['status' => ScheduleStatus::InProgress]);
                $locked->unit->update(['status' => UnitStatus::InUse]);
            }

            if (in_array($target, [BookingStatus::Completed, BookingStatus::Cancelled], true)) {
                $locked->schedule()->update(['status' => ScheduleStatus::Released]);
                $locked->unit->refresh();
                $this->schedules->syncUnit($locked->unit);
            }

            return $locked->fresh(['customer', 'package', 'unit.type', 'detail', 'payments', 'schedule']);
        }, 3);
    }
}
