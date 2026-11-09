<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\CompanyEvent;
use App\Models\Holiday;
use App\Models\Room;
use App\Models\WorkingHour;
use App\Services\SettingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function __construct(
        protected SettingService $settingService
    ) {}

    public function index(Request $request): View
    {
        $tz = $this->settingService->officeTimezone();
        $today = Carbon::today($tz);

        $selectedDate = $request->query('date', $today->toDateString());
        try {
            $currentDate = Carbon::parse($selectedDate, $tz);
        } catch (\Exception) {
            $currentDate = $today->copy();
            $selectedDate = $today->toDateString();
        }

        $prevDate = $currentDate->copy()->subDay()->toDateString();
        $nextDate = $currentDate->copy()->addDay()->toDateString();

        $dayOfWeek = $currentDate->dayOfWeekIso;
        $workingHour = WorkingHour::where('day_of_week', $dayOfWeek)->first();
        $holiday = Holiday::whereDate('holiday_date', $selectedDate)->first();

        $rooms = Room::with(['bookings' => function ($q) use ($selectedDate) {
            $q->confirmed()
                ->whereDate('booking_date', $selectedDate)
                ->orderBy('start_at');
        }, 'equipments'])
            ->active()
            ->orderBy('sort_order')
            ->get();

        $companyEvents = [];
        if ($this->settingService->showCompanyCalendar()) {
            $companyEvents = CompanyEvent::whereDate('event_date', $selectedDate)
                ->orderBy('start_time')
                ->get();
        }

        $timeStep = $this->settingService->timeStepMinutes();

        return view('pages.schedule.index', [
            'rooms' => $rooms,
            'selectedDate' => $selectedDate,
            'currentDate' => $currentDate,
            'prevDate' => $prevDate,
            'nextDate' => $nextDate,
            'todayDate' => $today->toDateString(),
            'workingHour' => $workingHour,
            'holiday' => $holiday,
            'companyEvents' => $companyEvents,
            'timeStep' => $timeStep,
        ]);
    }
}
