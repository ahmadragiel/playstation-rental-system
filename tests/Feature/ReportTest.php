<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\User;
use App\Services\ReportService;
use Database\Seeders\SettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SettingSeeder::class);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_report_uses_paid_payments_and_excludes_cancelled_from_ranking(): void
    {
        $booking = Booking::factory()->create([
            'status' => BookingStatus::Completed,
            'start_at' => now()->subDay()->setTime(14, 0),
            'end_at' => now()->subDay()->setTime(17, 0),
            'total_price' => 85000,
        ]);
        Payment::query()->create([
            'transaction_number' => 'PAY-REPORT-001',
            'booking_id' => $booking->id,
            'processed_by' => $this->admin->id,
            'method' => 'qris',
            'amount' => 85000,
            'status' => PaymentStatus::Paid,
            'paid_at' => now()->subDay()->setTime(18, 0),
        ]);
        Booking::factory()->create([
            'status' => BookingStatus::Cancelled,
            'start_at' => now()->subDays(2)->setTime(14, 0),
            'end_at' => now()->subDays(2)->setTime(17, 0),
            'total_price' => 50000,
        ]);

        $report = app(ReportService::class)->getReportData(['period' => 'month']);

        $this->assertSame(85000.0, $report['revenue']);
        $this->assertSame(2, $report['booking_count']);
        $this->assertSame(1, $report['completed_count']);
        $this->assertSame(1, $report['cancelled_count']);
        $this->assertCount(1, $report['top_packages']);
    }

    public function test_admin_can_view_and_export_csv_with_formula_injection_protection(): void
    {
        $customer = Customer::factory()->create(['name' => '=HYPERLINK("https://evil.test")']);
        $booking = Booking::factory()->create([
            'customer_id' => $customer->id,
            'status' => BookingStatus::Confirmed,
            'start_at' => now()->startOfMonth()->addHours(2),
            'end_at' => now()->startOfMonth()->addHours(5),
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.reports.index', ['period' => 'month']))
            ->assertSuccessful();

        $response = $this->actingAs($this->admin)->get(route('admin.reports.csv', ['period' => 'month']));
        $response->assertSuccessful();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Nexus Play - Laporan Rental', $content);
        $this->assertStringContainsString("'=HYPERLINK", $content);
        $this->assertStringContainsString($booking->booking_number, $content);
    }

    public function test_admin_can_export_pdf(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.reports.pdf', ['period' => 'month']))
            ->assertSuccessful()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_custom_report_range_is_limited(): void
    {
        $this->actingAs($this->admin)->get(route('admin.reports.index', [
            'period' => 'custom',
            'from' => now()->subYears(2)->toDateString(),
            'to' => now()->toDateString(),
        ]))->assertSessionHasErrors('to');
    }
}
