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
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected SlotCalculationService $slotCalculationService,
        protected CalendarLinkService $calendarLinkService,
        protected SettingService $settingService
    ) {}

    public function create(Request $request, Room $room): View
    {
        if (! $room->is_active) {
            abort(404, 'Room not found or currently unavailable.');
        }

        $room->load('equipments');

        $tz = $this->settingService->officeTimezone();
        $today = Carbon::today($tz);
        $maxDays = $this->settingService->maxDaysAhead();

        $selectedDate = $request->query('date', $today->toDateString());
        try {
            $parsedDate = Carbon::parse($selectedDate, $tz);
            if ($parsedDate->lessThan($today) || $parsedDate->greaterThan($today->copy()->addDays($maxDays))) {
                $selectedDate = $today->toDateString();
            }
        } catch (\Exception) {
            $selectedDate = $today->toDateString();
        }

        $slotData = $this->slotCalculationService->getAvailableSlots($room, $selectedDate);
        $allRooms = Room::active()->orderBy('sort_order')->get(['id', 'slug', 'name_en', 'name_sr', 'capacity', 'location']);

        $user = $request->user();

        return view('pages.bookings.create', [
            'room' => $room,
            'allRooms' => $allRooms,
            'selectedDate' => $selectedDate,
            'selectedStartTime' => $request->query('start'),
            'selectedPeople' => $request->query('people', 2),
            'slotData' => $slotData,
            'todayDate' => $today->toDateString(),
            'maxDate' => $today->copy()->addDays($maxDays)->toDateString(),
            'prefilledName' => $user?->name ?? '',
            'prefilledEmail' => $user?->email ?? '',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Spam Honeypot Protection
        if ($request->filled('website_url')) {
            throw ValidationException::withMessages([
                'title' => 'Spam submission detected.',
            ]);
        }

        $validated = $request->validate([
            'room_id' => ['required', 'exists:rooms,id'],
            'booking_date' => ['required', 'date'],
            'start_time' => ['required', 'regex:/^\d{2}:\d{2}$/'],
            'duration_minutes' => ['required', 'integer', 'min:15'],
            'title' => ['required', 'string', 'min:3', 'max:120'],
            'agenda' => ['nullable', 'string', 'max:1000'],
            'organizer_name' => ['required', 'string', 'min:2', 'max:100'],
            'organizer_email' => ['required', 'email', 'max:150'],
            'attendees_count' => ['required', 'integer', 'min:1'],
        ]);

        $validated['locale'] = app()->getLocale();

        $booking = $this->bookingService->createBooking($validated, $request->user());

        $successMsg = app()->getLocale() === 'sr_Latn'
            ? 'Rezervacija je uspešno kreirana! Poslat je potvrdni email.'
            : 'Booking successfully confirmed! A confirmation email has been dispatched.';

        return redirect()->route('bookings.confirmation', $booking->reference_code)
            ->with('status', $successMsg);
    }

    public function confirmation(Request $request, string $reference): View
    {
        $booking = Booking::with(['room.equipments', 'simulatedEmails'])
            ->where('reference_code', $reference)
            ->firstOrFail();

        $googleCalUrl = $this->calendarLinkService->googleCalendarUrl($booking);

        return view('pages.bookings.confirmation', [
            'booking' => $booking,
            'googleCalUrl' => $googleCalUrl,
        ]);
    }
}
