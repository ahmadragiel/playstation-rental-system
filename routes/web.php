<?php

use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PlaystationUnitController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ScheduleController as AdminScheduleController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\PublicBookingController;
use App\Http\Controllers\Public\PublicScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/paket-sewa', [PageController::class, 'packages'])->name('packages.index');
Route::get('/playstation', [PageController::class, 'units'])->name('units.index');
Route::get('/game', [PageController::class, 'games'])->name('games.index');
Route::get('/fasilitas', [PageController::class, 'facilities'])->name('facilities');
Route::get('/cara-sewa', [PageController::class, 'howItWorks'])->name('how-it-works');
Route::get('/kontak', [PageController::class, 'contact'])->name('contact');

Route::prefix('booking')->name('booking.')->group(function (): void {
    Route::get('/', [PublicBookingController::class, 'create'])->name('create');
    Route::post('/', [PublicBookingController::class, 'store'])->middleware('throttle:10,1')->name('store');
    Route::post('/quote', [PublicBookingController::class, 'quote'])->middleware('throttle:30,1')->name('quote');
    Route::post('/availability', [PublicBookingController::class, 'availability'])->middleware('throttle:30,1')->name('availability');
    Route::get('/success/{booking}', [PublicBookingController::class, 'success'])->name('success');
});

Route::get('/jadwal', [PublicScheduleController::class, 'index'])->name('schedule.index');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:6,1');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function (): void {
    Route::redirect('/', '/dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('bookings.status');

    Route::get('/jadwal', [AdminScheduleController::class, 'index'])->name('schedule');

    Route::get('/pelanggan', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/pelanggan/{customer}', [CustomerController::class, 'show'])->name('customers.show');

    Route::resource('units', PlaystationUnitController::class)->except('show');
    Route::resource('packages', PackageController::class)->except('show');
    Route::resource('games', GameController::class)->except('show');

    Route::get('/pembayaran', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/pembayaran', [PaymentController::class, 'store'])->name('payments.store');
    Route::patch('/pembayaran/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.status');

    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/export', [ReportController::class, 'exportCsv'])->name('reports.csv');
    Route::get('/laporan/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');

    Route::get('/pengaturan', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/pengaturan', [SettingController::class, 'update'])->name('settings.update');
});
