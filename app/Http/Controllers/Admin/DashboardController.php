<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\SimulatedEmail;
use App\Services\SettingService;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected SettingService $settingService
    ) {}

    public function index(): View
    {
        $tz = $this->settingService->officeTimezone();
        $today = Carbon::today($tz);
        $now = Carbon::now($tz);

        $todayBookingsCount = Booking::whereDate('booking_date', $today->toDateString())->count();
        $activeRoomsCount = Room::active()->count();

        $currentlyBusyCount = Booking::confirmed()
            ->where('start_at', '<=', $now)
            ->where('end_at', '>', $now)
            ->count();

        $cancelledTodayCount = Booking::cancelled()
            ->whereDate('cancelled_at', $today->toDateString())
            ->count();

        $upcomingToday = Booking::with('room')
            ->confirmed()
            ->whereDate('booking_date', $today->toDateString())
            ->where('end_at', '>=', $now)
            ->orderBy('start_at')
            ->take(5)
            ->get();

        $recentEmails = SimulatedEmail::orderByDesc('sent_at')
            ->take(6)
            ->get();

        return view('admin.dashboard', [
            'todayBookingsCount' => $todayBookingsCount,
            'activeRoomsCount' => $activeRoomsCount,
            'currentlyBusyCount' => $currentlyBusyCount,
            'cancelledTodayCount' => $cancelledTodayCount,
            'upcomingToday' => $upcomingToday,
            'recentEmails' => $recentEmails,
            'todayDate' => $today->toDateString(),
        ]);
    }
}
