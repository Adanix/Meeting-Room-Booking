<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Services\BookingService;
use App\Services\SettingService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookingManagementController extends Controller
{
    public function __construct(
        protected BookingService $bookingService,
        protected SettingService $settingService
    ) {}

    public function index(Request $request): View
    {
        $query = Booking::with(['room', 'createdByUser'])->orderByDesc('start_at');

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('reference_code', 'like', $s)
                    ->orWhere('title', 'like', $s)
                    ->orWhere('organizer_name', 'like', $s)
                    ->orWhere('organizer_email', 'like', $s);
            });
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        $bookings = $query->paginate(15)->withQueryString();
        $rooms = Room::orderBy('sort_order')->get();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'rooms' => $rooms,
            'filters' => $request->only(['search', 'room_id', 'status', 'date_from', 'date_to']),
        ]);
    }

    public function create(): View
    {
        $rooms = Room::active()->orderBy('sort_order')->get();
        $today = Carbon::today($this->settingService->officeTimezone())->toDateString();

        return view('admin.bookings.create', [
            'rooms' => $rooms,
            'todayDate' => $today,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
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

        $booking = $this->bookingService->createBooking($validated, $request->user());

        return redirect()->route('admin.bookings.index')
            ->with('status', "Booking {$booking->reference_code} created successfully.");
    }

    public function edit(Booking $booking): View
    {
        $rooms = Room::orderBy('sort_order')->get();
        $booking->load(['room', 'statusHistories.actorUser']);

        return view('admin.bookings.edit', [
            'booking' => $booking,
            'rooms' => $rooms,
        ]);
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
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

        $this->bookingService->rescheduleBooking($booking, $validated, $request->user());

        return redirect()->route('admin.bookings.index')
            ->with('status', "Booking {$booking->reference_code} updated successfully.");
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $this->bookingService->cancelBooking($booking, $validated['reason'] ?? null, $request->user(), 'admin');

        return back()->with('status', "Booking {$booking->reference_code} has been cancelled.");
    }

    public function restore(Booking $booking): RedirectResponse
    {
        $this->bookingService->restoreBooking($booking, request()->user());

        return back()->with('status', "Booking {$booking->reference_code} has been successfully restored.");
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $query = Booking::with('room')->orderByDesc('start_at');

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('reference_code', 'like', $s)
                    ->orWhere('title', 'like', $s)
                    ->orWhere('organizer_name', 'like', $s)
                    ->orWhere('organizer_email', 'like', $s);
            });
        }
        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('booking_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('booking_date', '<=', $request->date_to);
        }

        $bookings = $query->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="bookings-export-' . date('Ymd-His') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($bookings) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens Serbian Latin characters without mojibake
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Reference Code',
                'Status',
                'Room Name',
                'Location',
                'Date',
                'Start Time',
                'End Time',
                'Duration (Min)',
                'Title',
                'Agenda',
                'Organizer Name',
                'Organizer Email',
                'Attendees Count',
                'Created At',
                'Cancelled At',
            ]);

            foreach ($bookings as $b) {
                fputcsv($handle, [
                    $b->reference_code,
                    ucfirst($b->status),
                    $b->room ? $b->room->name_en : 'N/A',
                    $b->room ? $b->room->location : 'N/A',
                    $b->booking_date->format('Y-m-d'),
                    $b->start_at->format('H:i'),
                    $b->end_at->format('H:i'),
                    $b->duration_minutes,
                    $b->title,
                    $b->agenda,
                    $b->organizer_name,
                    $b->organizer_email,
                    $b->attendees_count,
                    $b->created_at->toDateTimeString(),
                    $b->cancelled_at ? $b->cancelled_at->toDateTimeString() : '',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
