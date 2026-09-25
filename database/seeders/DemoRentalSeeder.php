<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Enums\ScheduleStatus;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Payment;
use App\Models\PlaystationUnit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DemoRentalSeeder extends Seeder
{
    public function run(): void
    {
        if (Booking::query()->exists()) {
            return;
        }

        $admin = User::query()->where('role', 'admin')->firstOrFail();
        $data = [
            ['RNT-DEMO-1001', 'Dimas Pratama', '081211112221', 'PS5 Reguler', 'PS5-01', -7, 14, 0, BookingStatus::Completed, ScheduleStatus::Released, PaymentStatus::Paid, 'transfer', 'BANK-8891'],
            ['RNT-DEMO-1002', 'Nadia Putri', '081233334445', 'PS4 Reguler', 'PS4-01', -4, 18, 0, BookingStatus::Completed, ScheduleStatus::Released, PaymentStatus::Paid, 'qris', 'QRIS-4432'],
            ['RNT-DEMO-1003', 'Fajar Saputra', '081255556667', 'PS5 Reguler', 'PS5-02', -2, 11, 0, BookingStatus::Completed, ScheduleStatus::Released, PaymentStatus::Paid, 'cash', null],
            ['RNT-DEMO-1004', 'Raka Pratama', '081277778889', 'PS4 Reguler', 'PS4-02', 1, 11, 0, BookingStatus::Confirmed, ScheduleStatus::Reserved, null, null, null],
            ['RNT-DEMO-1005', 'Salsa Dewi', '081299990001', 'PS5 Reguler', 'PS5-01', 1, 15, 0, BookingStatus::Paid, ScheduleStatus::Reserved, PaymentStatus::Paid, 'qris', 'QRIS-9912'],
            ['RNT-DEMO-1006', 'Bagas Kurnia', '081200020003', 'PS5 VIP', 'PS5-VIP-01', 2, 19, 0, BookingStatus::Pending, ScheduleStatus::Reserved, null, null, null],
            ['RNT-DEMO-1007', 'Alya Rahma', '081211223344', 'PS4 Reguler', 'PS4-01', 0, 18, 0, BookingStatus::Paid, ScheduleStatus::Reserved, PaymentStatus::Paid, 'qris', 'QRIS-2026'],
        ];

        foreach ($data as $row) {
            [$number, $name, $whatsapp, $packageName, $unitCode, $dayOffset, $hour, $minute, $bookingStatus, $scheduleStatus, $paymentStatus, $method, $reference] = $row;
            $this->createBooking(
                $admin,
                $number,
                $name,
                $whatsapp,
                $packageName,
                $unitCode,
                now()->addDays($dayOffset)->setTime($hour, $minute),
                $bookingStatus,
                $scheduleStatus,
                $paymentStatus,
                $method,
                $reference,
            );
        }
    }

    private function createBooking(
        User $admin,
        string $number,
        string $customerName,
        string $whatsapp,
        string $packageName,
        string $unitCode,
        Carbon $startAt,
        BookingStatus $bookingStatus,
        ScheduleStatus $scheduleStatus,
        ?PaymentStatus $paymentStatus,
        ?string $method,
        ?string $reference,
    ): void {
        $package = Package::query()->where('name', $packageName)->firstOrFail();
        $unit = PlaystationUnit::query()->where('code', $unitCode)->firstOrFail();
        $endAt = $startAt->copy()->addMinutes($package->duration_minutes);
        $customer = Customer::query()->updateOrCreate(
            ['whatsapp' => '62'.substr($whatsapp, 2)],
            ['name' => $customerName, 'email' => Str::lower(Str::slug($customerName)).'@example.com'],
        );

        $booking = Booking::query()->updateOrCreate(
            ['booking_number' => $number],
            [
                'public_id' => (string) Str::uuid(),
                'customer_id' => $customer->id,
                'playstation_unit_id' => $unit->id,
                'package_id' => $package->id,
                'start_at' => $startAt,
                'end_at' => $endAt,
                'duration_minutes' => $package->duration_minutes,
                'subtotal' => $package->price,
                'total_price' => $package->price,
                'status' => $bookingStatus,
                'confirmed_at' => $bookingStatus !== BookingStatus::Pending ? $startAt->copy()->subDays(2) : null,
                'completed_at' => $bookingStatus === BookingStatus::Completed ? $endAt : null,
                'cancelled_at' => null,
                'created_by' => $admin->id,
            ],
        );

        $booking->detail()->updateOrCreate([], [
            'package_name' => $package->name,
            'playstation_type' => $unit->type->name,
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
            'facilities' => $package->facilities,
        ]);

        $booking->schedule()->updateOrCreate([], [
            'playstation_unit_id' => $unit->id,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'status' => $scheduleStatus,
        ]);

        if ($paymentStatus) {
            Payment::query()->updateOrCreate(
                ['transaction_number' => 'PAY-'.str_replace('RNT-', '', $number)],
                [
                    'booking_id' => $booking->id,
                    'processed_by' => $admin->id,
                    'method' => $method,
                    'amount' => $package->price,
                    'status' => $paymentStatus,
                    'reference' => $reference,
                    'paid_at' => $startAt->isFuture() ? now() : $startAt->copy()->addHour(),
                ],
            );
        }
    }
}
