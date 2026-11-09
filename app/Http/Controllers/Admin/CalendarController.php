<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Holiday;
use App\Models\Room;
use App\Services\SettingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function __construct(
        protected SettingService $settingService
    ) {}

    public function index(Request $request): View
    {
        $tz = $this->settingService->officeTimezone();
        $monthInput = $request->query('month', Carbon::today($tz)->format('Y-m'));

        try {
            $currentMonth = Carbon::parse("{$monthInput}-01", $tz);
        } catch (\Exception) {
            $currentMonth = Carbon::today($tz)->startOfMonth();
            $monthInput = $currentMonth->format('Y-m');
        }

        $prevMonth = $currentMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');

        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        // Calendar grid: Monday-first (European standard)
        $startOfGrid = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        $endOfGrid = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        $query = Booking::with('room')
            ->confirmed()
            ->whereBetween('booking_date', [$startOfGrid->toDateString(), $endOfGrid->toDateString()]);

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        $bookings = $query->orderBy('start_at')->get();
        $bookingsByDate = $bookings->groupBy(fn ($b) => $b->booking_date->toDateString());

        $holidays = Holiday::whereBetween('holiday_date', [$startOfGrid->toDateString(), $endOfGrid->toDateString()])
            ->get()
            ->keyBy(fn ($h) => $h->holiday_date->toDateString());

        $days = [];
        $cursor = $startOfGrid->copy();
        while ($cursor->lessThanOrEqualTo($endOfGrid)) {
            $dateStr = $cursor->toDateString();
            $days[] = [
                'date' => $cursor->copy(),
                'date_string' => $dateStr,
                'is_current_month' => $cursor->month === $currentMonth->month,
                'is_today' => $cursor->isToday(),
                'bookings' => $bookingsByDate->get($dateStr, collect()),
                'holiday' => $holidays->get($dateStr),
            ];
            $cursor->addDay();
        }

        $rooms = Room::orderBy('sort_order')->get();

        return view('admin.calendar', [
            'currentMonth' => $currentMonth,
            'monthInput' => $monthInput,
            'prevMonth' => $prevMonth,
            'nextMonth' => $nextMonth,
            'days' => $days,
            'rooms' => $rooms,
            'selectedRoomId' => $request->query('room_id'),
        ]);
    }
}
