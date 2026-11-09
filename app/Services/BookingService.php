<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\Holiday;
use App\Models\Room;
use App\Models\User;
use App\Models\WorkingHour;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(
        protected SettingService $settingService,
        protected SimulatedEmailDeliveryService $emailDeliveryService
    ) {}

    public function createBooking(array $data, ?User $actor = null): Booking
    {
        $tz = $this->settingService->officeTimezone();

        return DB::transaction(function () use ($data, $actor, $tz) {
            $room = Room::whereKey($data['room_id'])->lockForUpdate()->firstOrFail();

            if (! $room->is_active) {
                throw ValidationException::withMessages([
                    'room_id' => __('This room is currently not available for bookings.'),
                ]);
            }

            if ((int) $data['attendees_count'] > $room->capacity) {
                throw ValidationException::withMessages([
                    'attendees_count' => __('Attendee count exceeds room capacity of :max.', ['max' => $room->capacity]),
                ]);
            }

            $date = Carbon::parse($data['booking_date'], $tz)->startOfDay();
            $now = Carbon::now($tz);

            $maxDays = $this->settingService->maxDaysAhead();
            if ($date->lessThan($now->copy()->startOfDay()) || $date->greaterThan($now->copy()->addDays($maxDays)->endOfDay())) {
                throw ValidationException::withMessages([
                    'booking_date' => __('Booking date must be between today and :days days ahead.', ['days' => $maxDays]),
                ]);
            }

            // Holiday check
            $holiday = Holiday::whereDate('holiday_date', $date->toDateString())->first();
            if ($holiday) {
                throw ValidationException::withMessages([
                    'booking_date' => __('The office is closed on this date (:name).', ['name' => $holiday->localizedName()]),
                ]);
            }

            // Working hours check
            $dayOfWeek = $date->dayOfWeekIso;
            $workingHour = WorkingHour::where('day_of_week', $dayOfWeek)->first();
            if (! $workingHour || ! $workingHour->is_open) {
                throw ValidationException::withMessages([
                    'booking_date' => __('The office is closed on this day of the week.'),
                ]);
            }

            $startAt = Carbon::parse("{$data['booking_date']} {$data['start_time']}", $tz);
            $durationMinutes = (int) $data['duration_minutes'];
            $endAt = $startAt->copy()->addMinutes($durationMinutes);

            if ($date->isSameDay($now) && $startAt->lessThanOrEqualTo($now)) {
                throw ValidationException::withMessages([
                    'start_time' => __('Start time must be in the future.'),
                ]);
            }

            $openTime = Carbon::parse("{$data['booking_date']} {$workingHour->open_time}", $tz);
            $closeTime = Carbon::parse("{$data['booking_date']} {$workingHour->close_time}", $tz);

            if ($startAt->lessThan($openTime) || $endAt->greaterThan($closeTime)) {
                throw ValidationException::withMessages([
                    'start_time' => __('Booking time must fall within working hours (:open – :close).', [
                        'open' => substr($workingHour->open_time, 0, 5),
                        'close' => substr($workingHour->close_time, 0, 5),
                    ]),
                ]);
            }

            // Duration checks
            $minDuration = $this->settingService->minDurationMinutes();
            $maxDuration = $this->settingService->maxDurationMinutes();
            $step = $this->settingService->timeStepMinutes();

            if ($durationMinutes < $minDuration || $durationMinutes > $maxDuration || ($durationMinutes % $step !== 0)) {
                throw ValidationException::withMessages([
                    'duration_minutes' => __('Duration must be between :min and :max minutes in :step minute increments.', [
                        'min' => $minDuration,
                        'max' => $maxDuration,
                        'step' => $step,
                    ]),
                ]);
            }

            // Half-open interval overlap check: start_at < new_end AND end_at > new_start
            $overlapCount = Booking::where('room_id', $room->id)
                ->confirmed()
                ->where('start_at', '<', $endAt)
                ->where('end_at', '>', $startAt)
                ->count();

            if ($overlapCount > 0) {
                throw ValidationException::withMessages([
                    'start_time' => __('This time slot was just booked by another colleague. Please select another time.'),
                ]);
            }

            // Generate unique reference & token
            $datePrefix = $startAt->format('Ymd');
            $refCode = 'RM-' . $datePrefix . '-' . strtoupper(Str::random(4));
            while (Booking::where('reference_code', $refCode)->exists()) {
                $refCode = 'RM-' . $datePrefix . '-' . strtoupper(Str::random(4));
            }

            $token = bin2hex(random_bytes(24));

            $booking = Booking::create([
                'reference_code' => $refCode,
                'management_token' => $token,
                'room_id' => $room->id,
                'created_by_user_id' => $actor?->id,
                'booking_date' => $date->toDateString(),
                'start_at' => $startAt,
                'end_at' => $endAt,
                'duration_minutes' => $durationMinutes,
                'title' => $data['title'],
                'agenda' => $data['agenda'] ?? null,
                'organizer_name' => $data['organizer_name'],
                'organizer_email' => $data['organizer_email'],
                'attendees_count' => (int) $data['attendees_count'],
                'status' => 'confirmed',
                'locale' => $data['locale'] ?? app()->getLocale(),
            ]);

            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'actor_user_id' => $actor?->id,
                'action' => 'created',
                'new_state' => $booking->toArray(),
                'note' => 'Initial booking created',
            ]);

            $this->emailDeliveryService->sendBookingCreated($booking);

            return $booking;
        });
    }

    public function rescheduleBooking(Booking $booking, array $data, ?User $actor = null): Booking
    {
        $tz = $this->settingService->officeTimezone();

        return DB::transaction(function () use ($booking, $data, $actor, $tz) {
            $isStaff = $actor && $actor->hasRole(['admin', 'coordinator']);

            if (! $isStaff && ! $booking->canBeManaged($tz)) {
                throw ValidationException::withMessages([
                    'manage' => __('Bookings cannot be changed after the meeting has started or if cancelled.'),
                ]);
            }

            $roomId = $data['room_id'] ?? $booking->room_id;
            $room = Room::whereKey($roomId)->lockForUpdate()->firstOrFail();

            $attendeesCount = (int) ($data['attendees_count'] ?? $booking->attendees_count);
            if ($attendeesCount > $room->capacity) {
                throw ValidationException::withMessages([
                    'attendees_count' => __('Attendee count exceeds room capacity of :max.', ['max' => $room->capacity]),
                ]);
            }

            $dateString = $data['booking_date'] ?? $booking->booking_date->toDateString();
            $startTime = $data['start_time'] ?? $booking->start_at->format('H:i');
            $durationMinutes = (int) ($data['duration_minutes'] ?? $booking->duration_minutes);

            $startAt = Carbon::parse("{$dateString} {$startTime}", $tz);
            $endAt = $startAt->copy()->addMinutes($durationMinutes);
            $now = Carbon::now($tz);

            if (! $isStaff && $startAt->lessThanOrEqualTo($now)) {
                throw ValidationException::withMessages([
                    'start_time' => __('Rescheduled start time must be in the future.'),
                ]);
            }

            // Check working hours & holidays
            $holiday = Holiday::whereDate('holiday_date', $dateString)->first();
            if ($holiday) {
                throw ValidationException::withMessages([
                    'booking_date' => __('The office is closed on this date (:name).', ['name' => $holiday->localizedName()]),
                ]);
            }

            $dayOfWeek = $startAt->dayOfWeekIso;
            $workingHour = WorkingHour::where('day_of_week', $dayOfWeek)->first();
            if (! $workingHour || ! $workingHour->is_open) {
                throw ValidationException::withMessages([
                    'booking_date' => __('The office is closed on this day of the week.'),
                ]);
            }

            $openTime = Carbon::parse("{$dateString} {$workingHour->open_time}", $tz);
            $closeTime = Carbon::parse("{$dateString} {$workingHour->close_time}", $tz);
            if ($startAt->lessThan($openTime) || $endAt->greaterThan($closeTime)) {
                throw ValidationException::withMessages([
                    'start_time' => __('Booking time must fall within working hours (:open – :close).', [
                        'open' => substr($workingHour->open_time, 0, 5),
                        'close' => substr($workingHour->close_time, 0, 5),
                    ]),
                ]);
            }

            // Check overlap excluding this booking
            $overlapCount = Booking::where('room_id', $room->id)
                ->where('id', '!=', $booking->id)
                ->confirmed()
                ->where('start_at', '<', $endAt)
                ->where('end_at', '>', $startAt)
                ->count();

            if ($overlapCount > 0) {
                throw ValidationException::withMessages([
                    'start_time' => __('The selected slot conflicts with another reservation.'),
                ]);
            }

            $previousState = $booking->toArray();

            $booking->update([
                'room_id' => $room->id,
                'booking_date' => $startAt->toDateString(),
                'start_at' => $startAt,
                'end_at' => $endAt,
                'duration_minutes' => $durationMinutes,
                'title' => $data['title'] ?? $booking->title,
                'agenda' => array_key_exists('agenda', $data) ? $data['agenda'] : $booking->agenda,
                'organizer_name' => $data['organizer_name'] ?? $booking->organizer_name,
                'organizer_email' => $data['organizer_email'] ?? $booking->organizer_email,
                'attendees_count' => $attendeesCount,
            ]);

            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'actor_user_id' => $actor?->id,
                'action' => $isStaff ? 'edited' : 'rescheduled',
                'previous_state' => $previousState,
                'new_state' => $booking->fresh()->toArray(),
                'note' => $isStaff ? 'Booking edited by staff' : 'Booking rescheduled by organizer',
            ]);

            $this->emailDeliveryService->sendBookingMoved($booking);

            return $booking;
        });
    }

    public function cancelBooking(Booking $booking, ?string $reason = null, ?User $actor = null, string $cancelledBy = 'organizer'): Booking
    {
        $tz = $this->settingService->officeTimezone();

        return DB::transaction(function () use ($booking, $reason, $actor, $cancelledBy, $tz) {
            $isStaff = $actor && $actor->hasRole(['admin', 'coordinator']);

            if (! $isStaff && ! $booking->canBeManaged($tz)) {
                throw ValidationException::withMessages([
                    'manage' => __('Bookings cannot be cancelled after the meeting has started or if already cancelled.'),
                ]);
            }

            $previousState = $booking->toArray();

            $booking->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
                'cancelled_at' => Carbon::now($tz),
                'cancelled_by' => $cancelledBy,
            ]);

            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'actor_user_id' => $actor?->id,
                'action' => 'cancelled',
                'previous_state' => $previousState,
                'new_state' => $booking->fresh()->toArray(),
                'note' => $reason ? "Cancelled: {$reason}" : 'Booking cancelled',
            ]);

            $this->emailDeliveryService->sendBookingCancelled($booking);

            return $booking;
        });
    }

    public function restoreBooking(Booking $booking, ?User $actor = null): Booking
    {
        $tz = $this->settingService->officeTimezone();

        return DB::transaction(function () use ($booking, $actor, $tz) {
            $room = Room::whereKey($booking->room_id)->lockForUpdate()->firstOrFail();

            // Check if slot was occupied by another booking
            $conflict = Booking::where('room_id', $room->id)
                ->where('id', '!=', $booking->id)
                ->confirmed()
                ->where('start_at', '<', $booking->end_at)
                ->where('end_at', '>', $booking->start_at)
                ->first();

            if ($conflict) {
                throw ValidationException::withMessages([
                    'restore' => __("Cannot restore booking :ref because the slot is currently occupied by ':conflict' (:conflict_ref). Edit the time or room first.", [
                        'ref' => $booking->reference_code,
                        'conflict' => $conflict->title,
                        'conflict_ref' => $conflict->reference_code,
                    ]),
                ]);
            }

            $previousState = $booking->toArray();

            $booking->update([
                'status' => 'confirmed',
                'cancellation_reason' => null,
                'cancelled_at' => null,
                'cancelled_by' => null,
            ]);

            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'actor_user_id' => $actor?->id,
                'action' => 'restored',
                'previous_state' => $previousState,
                'new_state' => $booking->fresh()->toArray(),
                'note' => 'Booking restored by admin',
            ]);

            $this->emailDeliveryService->sendBookingRestored($booking);

            return $booking;
        });
    }
}
