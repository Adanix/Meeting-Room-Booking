<?php

namespace App\Services;

use App\Models\Booking;

class CalendarLinkService
{
    public function googleCalendarUrl(Booking $booking): string
    {
        $startUtc = $booking->start_at->copy()->timezone('UTC')->format('Ymd\THis\Z');
        $endUtc = $booking->end_at->copy()->timezone('UTC')->format('Ymd\THis\Z');

        $details = ($booking->agenda ? $booking->agenda . "\n\n" : '')
            . 'Booking Reference: ' . $booking->reference_code . "\n"
            . 'Room: ' . ($booking->room ? $booking->room->localizedName() . ' (' . $booking->room->location . ')' : '') . "\n"
            . 'Attendees: ' . $booking->attendees_count . ' people';

        $location = $booking->room
            ? $booking->room->localizedName() . ' (' . $booking->room->location . ')'
            : 'Meeting Room';

        $params = http_build_query([
            'action' => 'TEMPLATE',
            'text' => $booking->title,
            'dates' => "{$startUtc}/{$endUtc}",
            'details' => $details,
            'location' => $location,
        ]);

        return 'https://calendar.google.com/calendar/render?' . $params;
    }
}
