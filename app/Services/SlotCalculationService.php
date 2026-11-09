<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Holiday;
use App\Models\Room;
use App\Models\WorkingHour;
use Carbon\Carbon;

class SlotCalculationService
{
    public function __construct(
        protected SettingService $settingService
    ) {}

    public function getAvailableSlots(Room $room, string $dateString, ?int $excludeBookingId = null): array
    {
        $tz = $this->settingService->officeTimezone();
        $date = Carbon::parse($dateString, $tz);
        $dayOfWeek = $date->dayOfWeekIso;

        // 1. Holiday Check
        $holiday = Holiday::whereDate('holiday_date', $dateString)->first();
        if ($holiday) {
            return [
                'is_holiday' => true,
                'holiday_name_en' => $holiday->name_en,
                'holiday_name_sr' => $holiday->name_sr,
                'is_closed' => true,
                'slots' => [],
            ];
        }

        // 2. Working Hours Check
        $workingHour = WorkingHour::where('day_of_week', $dayOfWeek)->first();
        if (! $workingHour || ! $workingHour->is_open) {
            return [
                'is_holiday' => false,
                'is_closed' => true,
                'closed_reason' => 'Weekend / Non-working day',
                'slots' => [],
            ];
        }

        $step = $this->settingService->timeStepMinutes();
        $minDuration = $this->settingService->minDurationMinutes();
        $maxDuration = $this->settingService->maxDurationMinutes();

        $openTime = Carbon::parse("{$dateString} {$workingHour->open_time}", $tz);
        $closeTime = Carbon::parse("{$dateString} {$workingHour->close_time}", $tz);
        $now = Carbon::now($tz);
        $isToday = $date->isSameDay($now);

        // 3. Fetch existing confirmed bookings for this room on this date
        $query = Booking::where('room_id', $room->id)
            ->confirmed()
            ->whereDate('booking_date', $dateString);

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        $existingBookings = $query->orderBy('start_at')->get();

        $availableSlots = [];
        $candidateStart = $openTime->copy();

        while ($candidateStart->copy()->addMinutes($minDuration)->lessThanOrEqualTo($closeTime)) {
            // Cannot book in the past if today
            if ($isToday && $candidateStart->lessThanOrEqualTo($now)) {
                $candidateStart->addMinutes($step);
                continue;
            }

            $minEnd = $candidateStart->copy()->addMinutes($minDuration);

            // Check if minimal slot [candidateStart, minEnd) overlaps existing booking
            $hasMinOverlap = false;
            foreach ($existingBookings as $b) {
                $bStart = $b->start_at->copy()->timezone($tz);
                $bEnd = $b->end_at->copy()->timezone($tz);

                // Overlap condition: start < other_end && end > other_start
                if ($candidateStart->lessThan($bEnd) && $minEnd->greaterThan($bStart)) {
                    $hasMinOverlap = true;
                    break;
                }
            }

            if (! $hasMinOverlap) {
                // Calculate valid durations from this start time
                $validDurations = [];
                for ($d = $minDuration; $d <= $maxDuration; $d += $step) {
                    $testEnd = $candidateStart->copy()->addMinutes($d);
                    if ($testEnd->greaterThan($closeTime)) {
                        break;
                    }

                    // Check if [candidateStart, testEnd) overlaps any booking
                    $overlap = false;
                    foreach ($existingBookings as $b) {
                        $bStart = $b->start_at->copy()->timezone($tz);
                        $bEnd = $b->end_at->copy()->timezone($tz);

                        if ($candidateStart->lessThan($bEnd) && $testEnd->greaterThan($bStart)) {
                            $overlap = true;
                            break;
                        }
                    }

                    if ($overlap) {
                        break; // Since durations increase monotonically, stop at first collision
                    }

                    $validDurations[] = [
                        'minutes' => $d,
                        'end_time' => $testEnd->format('H:i'),
                    ];
                }

                if (! empty($validDurations)) {
                    $availableSlots[] = [
                        'time' => $candidateStart->format('H:i'),
                        'datetime' => $candidateStart->toIso8601String(),
                        'valid_durations' => $validDurations,
                    ];
                }
            }

            $candidateStart->addMinutes($step);
        }

        return [
            'is_holiday' => false,
            'is_closed' => false,
            'open_time' => substr($workingHour->open_time, 0, 5),
            'close_time' => substr($workingHour->close_time, 0, 5),
            'slots' => $availableSlots,
        ];
    }
}
