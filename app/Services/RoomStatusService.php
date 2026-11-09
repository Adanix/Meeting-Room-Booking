<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Holiday;
use App\Models\Room;
use App\Models\WorkingHour;
use Carbon\Carbon;

class RoomStatusService
{
    public function __construct(
        protected SettingService $settingService
    ) {}

    public function getRoomStatus(Room $room, ?Carbon $at = null, ?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $tz = $this->settingService->officeTimezone();
        $now = $at ? $at->copy()->timezone($tz) : Carbon::now($tz);
        $todayDate = $now->toDateString();
        $dayOfWeek = $now->dayOfWeekIso; // 1 (Mon) - 7 (Sun)

        // 1. Check Holiday
        $isHoliday = Holiday::whereDate('holiday_date', $todayDate)->first();
        $workingHour = WorkingHour::where('day_of_week', $dayOfWeek)->first();

        $currentTime = $now->format('H:i:s');
        $isWorkingHours = $workingHour
            && $workingHour->is_open
            && $currentTime >= $workingHour->open_time
            && $currentTime < $workingHour->close_time;

        if ($isHoliday || ! $isWorkingHours) {
            $reason = $isHoliday
                ? ($locale === 'sr_Latn' ? $isHoliday->name_sr : $isHoliday->name_en)
                : ($workingHour && ! $workingHour->is_open
                    ? ($locale === 'sr_Latn' ? 'Kancelarija je zatvorena vikendom' : 'Office is closed on weekends')
                    : ($locale === 'sr_Latn' ? 'Van radnog vremena' : 'Outside working hours'));

            return [
                'code' => 'closed',
                'label' => $locale === 'sr_Latn' ? 'Zatvoreno' : 'Closed',
                'badge_class' => 'bg-slate-100 text-slate-700 border-slate-300',
                'dot_class' => 'bg-slate-400',
                'subtitle' => $reason,
                'current_booking' => null,
                'next_booking' => null,
            ];
        }

        // 2. Check Active Booking
        $activeBooking = Booking::where('room_id', $room->id)
            ->confirmed()
            ->where('start_at', '<=', $now)
            ->where('end_at', '>', $now)
            ->first();

        if ($activeBooking) {
            $until = $activeBooking->end_at->timezone($tz)->format('H:i');
            $subtitle = $locale === 'sr_Latn'
                ? "Zauzeto do {$until} ({$activeBooking->title})"
                : "Busy until {$until} ({$activeBooking->title})";

            return [
                'code' => 'busy',
                'label' => $locale === 'sr_Latn' ? 'Zauzeto' : 'Busy',
                'badge_class' => 'bg-rose-50 text-rose-700 border-rose-200',
                'dot_class' => 'bg-rose-500',
                'subtitle' => $subtitle,
                'current_booking' => $activeBooking,
                'next_booking' => null,
            ];
        }

        // 3. Status is Free - Find next booking today
        $nextBooking = Booking::where('room_id', $room->id)
            ->confirmed()
            ->where('start_at', '>', $now)
            ->whereDate('booking_date', $todayDate)
            ->orderBy('start_at')
            ->first();

        $freeUntilTime = $nextBooking
            ? $nextBooking->start_at->timezone($tz)->format('H:i')
            : substr($workingHour->close_time, 0, 5);

        $subtitle = $locale === 'sr_Latn'
            ? "Slobodno do {$freeUntilTime}"
            : "Free until {$freeUntilTime}";

        return [
            'code' => 'free',
            'label' => $locale === 'sr_Latn' ? 'Slobodno' : 'Free',
            'badge_class' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'dot_class' => 'bg-emerald-500',
            'subtitle' => $subtitle,
            'current_booking' => null,
            'next_booking' => $nextBooking,
        ];
    }
}
