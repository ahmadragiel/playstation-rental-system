<?php

namespace App\Services;

use App\Enums\ScheduleStatus;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Package;
use App\Models\PlaystationUnit;
use App\Models\Schedule;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(private readonly OperatingHoursService $operatingHours) {}

    /** @param array<string, mixed> $data */
    public function create(array $data, ?int $createdBy = null): Booking
    {
        return DB::transaction(function () use ($data, $createdBy): Booking {
            $package = Package::query()
                ->with('type')
                ->whereKey($data['package_id'])
                ->where('is_active', true)
                ->first();

            if (! $package) {
                throw ValidationException::withMessages(['package_id' => 'Paket tidak tersedia.']);
            }

            $requestedDuration = (int) ($data['duration_minutes'] ?? $package->duration_minutes);
            if ($requestedDuration !== $package->duration_minutes) {
                throw ValidationException::withMessages([
                    'duration_minutes' => 'Durasi harus sesuai dengan durasi paket.',
                ]);
            }

            $unit = PlaystationUnit::query()
                ->with('type')
                ->whereKey($data['playstation_unit_id'])
                ->lockForUpdate()
                ->first();

            if (! $unit || ! $unit->is_active) {
                throw ValidationException::withMessages(['playstation_unit_id' => 'Unit tidak tersedia.']);
            }

            if ($unit->status->value === 'maintenance') {
                throw ValidationException::withMessages([
                    'playstation_unit_id' => 'Unit sedang dalam maintenance.',
                ]);
            }

            if ($unit->playstation_type_id !== $package->playstation_type_id) {
                throw ValidationException::withMessages([
                    'playstation_unit_id' => 'Unit tidak cocok dengan jenis PlayStation paket.',
                ]);
            }

            $startAt = CarbonImmutable::createFromFormat('Y-m-d H:i', $data['booking_date'].' '.$data['start_time'])->startOfSecond();
            $endAt = $startAt->addMinutes($package->duration_minutes);

            if ($startAt->isBefore(now()->addMinutes(30))) {
                throw ValidationException::withMessages([
                    'booking_date' => 'Waktu booking minimal 30 menit dari sekarang.',
                ]);
            }

            $this->operatingHours->assertWithin($startAt, $endAt);

            $hasConflict = Schedule::query()
                ->where('playstation_unit_id', $unit->id)
                ->whereIn('status', [ScheduleStatus::Reserved->value, ScheduleStatus::InProgress->value])
                ->where('start_at', '<', $endAt)
                ->where('end_at', '>', $startAt)
                ->exists();

            if ($hasConflict) {
                throw ValidationException::withMessages([
                    'playstation_unit_id' => 'Unit sudah dibooking pada waktu tersebut. Pilih jam atau unit lain.',
                ]);
            }

            $whatsapp = $this->normalizeWhatsapp($data['customer_whatsapp']);
            $customer = Customer::query()->firstOrNew(['whatsapp' => $whatsapp]);
            $customer->fill([
                'name' => $data['customer_name'],
                'email' => $data['customer_email'] ?? null,
            ]);
            $customer->whatsapp = $whatsapp;
            $customer->save();

            $booking = Booking::query()->create([
                'public_id' => (string) Str::uuid(),
                'booking_number' => $this->generateBookingNumber(),
                'customer_id' => $customer->id,
                'playstation_unit_id' => $unit->id,
                'package_id' => $package->id,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'duration_minutes' => $package->duration_minutes,
                'subtotal' => $package->price,
                'total_price' => $package->price,
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
                'created_by' => $createdBy,
            ]);

            $booking->detail()->create([
                'package_name' => $package->name,
                'playstation_type' => $package->type->name,
                'unit_code' => $unit->code,
                'unit_name' => $unit->name,
                'customer_name' => $customer->name,
                'customer_whatsapp' => $customer->whatsapp,
                'customer_email' => $customer->email,
                'package_price' => $package->price,
                'duration_minutes' => $package->duration_minutes,
                'total_price' => $package->price,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'facilities' => $package->facilities ?? [],
            ]);

            $booking->schedule()->create([
                'playstation_unit_id' => $unit->id,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'status' => ScheduleStatus::Reserved,
            ]);

            return $booking->load(['customer', 'package', 'unit.type', 'detail', 'schedule']);
        }, 3);
    }

    /** @param array<string, mixed> $data */
    public function quote(array $data): array
    {
        $package = Package::query()->whereKey($data['package_id'] ?? null)->where('is_active', true)->firstOrFail();
        $startAt = CarbonImmutable::createFromFormat('Y-m-d H:i', $data['booking_date'].' '.$data['start_time'])->startOfSecond();
        $endAt = $startAt->addMinutes($package->duration_minutes);

        if ($startAt->isBefore(now()->addMinutes(30))) {
            throw ValidationException::withMessages([
                'booking_date' => 'Waktu booking minimal 30 menit dari sekarang.',
            ]);
        }

        $this->operatingHours->assertWithin($startAt, $endAt);

        return [
            'package_id' => $package->id,
            'package_name' => $package->name,
            'duration_minutes' => $package->duration_minutes,
            'duration_label' => $package->duration_label,
            'start_at' => $startAt->toIso8601String(),
            'end_at' => $endAt->toIso8601String(),
            'start_time' => $startAt->format('H:i'),
            'end_time' => $endAt->format('H:i'),
            'total_price' => (float) $package->price,
        ];
    }

    /** @param array<string, mixed> $data */
    public function availableUnits(array $data): Collection
    {
        $package = Package::query()->whereKey($data['package_id'] ?? null)->where('is_active', true)->firstOrFail();
        $startAt = CarbonImmutable::createFromFormat('Y-m-d H:i', $data['booking_date'].' '.$data['start_time'])->startOfSecond();
        $endAt = $startAt->addMinutes($package->duration_minutes);

        return PlaystationUnit::query()
            ->with(['type', 'games:id,name'])
            ->where('playstation_type_id', $package->playstation_type_id)
            ->where('is_active', true)
            ->where('status', '!=', 'maintenance')
            ->whereDoesntHave('schedules', function ($query) use ($startAt, $endAt): void {
                $query->whereIn('status', [ScheduleStatus::Reserved->value, ScheduleStatus::InProgress->value])
                    ->where('start_at', '<', $endAt)
                    ->where('end_at', '>', $startAt);
            })
            ->orderBy('code')
            ->get();
    }

    private function generateBookingNumber(): string
    {
        do {
            $number = 'RNT-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (Booking::query()->where('booking_number', $number)->exists());

        return $number;
    }

    private function normalizeWhatsapp(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            $digits = '62'.substr($digits, 1);
        }

        return $digits;
    }
}
