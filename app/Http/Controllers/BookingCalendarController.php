<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\IcsGeneratorService;
use Symfony\Component\HttpFoundation\Response;

class BookingCalendarController extends Controller
{
    public function __construct(
        protected IcsGeneratorService $icsGeneratorService
    ) {}

    public function downloadIcs(string $reference): Response
    {
        $booking = Booking::with('room')
            ->where('reference_code', $reference)
            ->firstOrFail();

        $icsContent = $this->icsGeneratorService->generate($booking);

        return response($icsContent, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="booking-' . $reference . '.ics"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}
