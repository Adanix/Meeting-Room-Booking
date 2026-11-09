<?php

use App\Http\Controllers\Admin\BookingManagementController as AdminBookingController;
use App\Http\Controllers\Admin\CalendarController as AdminCalendarController;
use App\Http\Controllers\Admin\CompanyEventController as AdminCompanyEventController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HolidayController as AdminHolidayController;
use App\Http\Controllers\Admin\RoomManagementController as AdminRoomController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\SimulatedEmailController as AdminEmailController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BookingCalendarController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BookingSelfServiceController;
use App\Http\Controllers\BookingSlotController;
use App\Http\Controllers\DemoQuickLoginController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\RoomPortalController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes (Canonical Contract - PRD Section 0.1 & 25.1)
|--------------------------------------------------------------------------
*/

// P0-01 & P0-02: Public Marketing & Documentation
Route::get('/', [PublicPageController::class, 'home'])->name('public.home');
Route::get('/docs', [PublicPageController::class, 'docs'])->name('public.docs');

// Locale Switcher
Route::post('/locale', [LocaleController::class, 'switch'])->name('locale.switch');

// P0-03 & P0-04: Demo Persona Quick-Login & Demo Reset
Route::middleware(['EnsureDemoModeEnabled'])->group(function () {
    Route::get('/demo/login', [DemoQuickLoginController::class, 'show'])->name('demo.login');
    Route::post('/demo/quick-login', [DemoQuickLoginController::class, 'login'])
        ->middleware('throttle:20,1')
        ->name('demo.quick-login');
    Route::post('/demo/reset', [DemoQuickLoginController::class, 'resetDemoData'])
        ->middleware('throttle:5,1')
        ->name('demo.reset');
});

// Standard Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('login.store');
});
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Employee & Public Room Booking Portal
Route::get('/rooms', [RoomPortalController::class, 'index'])->name('rooms.index');
Route::get('/rooms/{room:slug}/book', [BookingController::class, 'create'])->name('rooms.book');
Route::get('/api/rooms/{room}/available-slots', [BookingSlotController::class, 'index'])->name('api.rooms.slots');

Route::post('/bookings', [BookingController::class, 'store'])
    ->middleware('throttle:15,1')
    ->name('bookings.store');
Route::get('/bookings/{reference}/confirmation', [BookingController::class, 'confirmation'])->name('bookings.confirmation');
Route::get('/bookings/{reference}/ics', [BookingCalendarController::class, 'downloadIcs'])->name('bookings.ics');

// Self-Service Manage Booking via Token
Route::get('/bookings/manage/{token}', [BookingSelfServiceController::class, 'show'])->name('bookings.manage');
Route::put('/bookings/manage/{token}', [BookingSelfServiceController::class, 'update'])
    ->middleware('throttle:15,1')
    ->name('bookings.manage.update');
Route::delete('/bookings/manage/{token}', [BookingSelfServiceController::class, 'cancel'])
    ->middleware('throttle:15,1')
    ->name('bookings.manage.cancel');

// Daily All-Rooms Schedule
Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');

// Facilities & Office Admin Panel
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,coordinator'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Bookings Management
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [AdminBookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [AdminBookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/export', [AdminBookingController::class, 'exportCsv'])->name('bookings.export');
    Route::get('/bookings/{booking}/edit', [AdminBookingController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{booking}', [AdminBookingController::class, 'update'])->name('bookings.update');
    Route::post('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/restore', [AdminBookingController::class, 'restore'])->name('bookings.restore');

    // Calendar View
    Route::get('/calendar', [AdminCalendarController::class, 'index'])->name('calendar');

    // Rooms (Read for coordinator, write for admin)
    Route::get('/rooms', [AdminRoomController::class, 'index'])->name('rooms.index');

    // Settings (Read for coordinator, write for admin)
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');

    // Simulated Email Outbox
    Route::get('/emails', [AdminEmailController::class, 'index'])->name('emails.index');

    // Admin-Only Mutations
    Route::middleware('role:admin')->group(function () {
        Route::get('/rooms/create', [AdminRoomController::class, 'create'])->name('rooms.create');
        Route::post('/rooms', [AdminRoomController::class, 'store'])->name('rooms.store');
        Route::get('/rooms/{room}/edit', [AdminRoomController::class, 'edit'])->name('rooms.edit');
        Route::put('/rooms/{room}', [AdminRoomController::class, 'update'])->name('rooms.update');
        Route::patch('/rooms/{room}/toggle', [AdminRoomController::class, 'toggleActive'])->name('rooms.toggle');

        Route::put('/settings', [AdminSettingsController::class, 'update'])->name('settings.update');
        Route::post('/holidays', [AdminHolidayController::class, 'store'])->name('holidays.store');
        Route::delete('/holidays/{holiday}', [AdminHolidayController::class, 'destroy'])->name('holidays.destroy');
        Route::post('/company-events', [AdminCompanyEventController::class, 'store'])->name('company-events.store');
        Route::delete('/company-events/{companyEvent}', [AdminCompanyEventController::class, 'destroy'])->name('company-events.destroy');
    });
});
