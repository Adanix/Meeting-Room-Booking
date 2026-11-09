<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Services\BookingService;
use App\Services\CalendarLinkService;
use App\Services\SettingService;
use App\Services\SlotCalculationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingSelfServiceController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected SlotCalculationService $slotCalculationService,
        protected CalendarLinkService $calendarLinkService,
        protected SettingService $settingService
    ) {}

    public function show(string $token): View
    {
        $booking = Booking::with(['room.equipments'])
            ->where('management_token', $token)
            ->firstOrFail();

        $tz = $this->settingService->officeTimezone();
        $canManage = $booking->canBeManaged($tz);
        $today = Carbon::today($tz);
        $maxDays = $this->settingService->maxDaysAhead();

        $allRooms = Room::active()->orderBy('sort_order')->get();
        $slotData = $canManage
            ? $this->slotCalculationService->getAvailableSlots($booking->room, $booking->booking_date->toDateString(), $booking->id)
            : ['slots' => []];

        $googleCalUrl = $this->calendarLinkService->googleCalendarUrl($booking);

        return view('pages.bookings.manage', [
            'booking' => $booking,
            'canManage' => $canManage,
            'allRooms' => $allRooms,
            'slotData' => $slotData,
            'todayDate' => $today->toDateString(),
            'maxDate' => $today->copy()->addDays($maxDays)->toDateString(),
            'googleCalUrl' => $googleCalUrl,
        ]);
    }

    public function update(Request $request, string $token): RedirectResponse
    {
        $booking = Booking::where('management_token', $token)->firstOrFail();

        $validated = $request->validate([
            'room_id' => ['nullable', 'exists:rooms,id'],
            'booking_date' => ['required', 'date'],
            'start_time' => ['required', 'regex:/^\d{2}:\d{2}$/'],
            'duration_minutes' => ['required', 'integer', 'min:15'],
            'title' => ['nullable', 'string', 'min:3', 'max:120'],
            'agenda' => ['nullable', 'string', 'max:1000'],
            'organizer_name' => ['nullable', 'string', 'min:2', 'max:100'],
            'organizer_email' => ['nullable', 'email', 'max:150'],
            'attendees_count' => ['nullable', 'integer', 'min:1'],
        ]);

        $validated['room_id'] = $validated['room_id'] ?? $booking->room_id;
        $validated['title'] = $validated['title'] ?? $booking->title;
        $validated['agenda'] = $validated['agenda'] ?? $booking->agenda;
        $validated['organizer_name'] = $validated['organizer_name'] ?? $booking->organizer_name;
        $validated['organizer_email'] = $validated['organizer_email'] ?? $booking->organizer_email;
        $validated['attendees_count'] = $validated['attendees_count'] ?? $booking->attendees_count;

        $this->bookingService->rescheduleBooking($booking, $validated, $request->user());

        $msg = app()->getLocale() === 'sr_Latn'
            ? 'Rezervacija je uspešno promenjena. Poslato je obaveštenje na vašu e-poštu.'
            : 'Booking has been successfully updated. Notification emails have been dispatched.';

        return back()->with('status', $msg);
    }

    public function cancel(Request $request, string $token): RedirectResponse
    {
        $booking = Booking::where('management_token', $token)->firstOrFail();

        $reason = $request->input('reason') ?? $request->input('cancellation_reason');

        $this->bookingService->cancelBooking($booking, $reason, $request->user(), 'organizer');

        $msg = app()->getLocale() === 'sr_Latn'
            ? 'Rezervacija je otkazana. Sala je sada slobodna za druge kolege.'
            : 'Booking has been cancelled. The room is now available for other colleagues.';

        return back()->with('status', $msg);
    }
}
