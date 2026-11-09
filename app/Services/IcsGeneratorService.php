<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;

class IcsGeneratorService
{
    public function generate(Booking $booking): string
    {
        $nowUtc = Carbon::now('UTC')->format('Ymd\THis\Z');
        $startUtc = $booking->start_at->copy()->timezone('UTC')->format('Ymd\THis\Z');
        $endUtc = $booking->end_at->copy()->timezone('UTC')->format('Ymd\THis\Z');

        $status = $booking->isCancelled() ? 'CANCELLED' : 'CONFIRMED';
        $location = $this->escapeIcsString($booking->room ? "{$booking->room->localizedName()} ({$booking->room->location})" : 'Meeting Room');
        $summary = $this->escapeIcsString($booking->title);

        $descriptionText = ($booking->agenda ? $booking->agenda . "\n\n" : '')
            . "Booking Reference: {$booking->reference_code}\n"
            . "Organizer: {$booking->organizer_name} ({$booking->organizer_email})\n"
            . "Attendees: {$booking->attendees_count}";

        $description = $this->escapeIcsString($descriptionText);
        $organizerName = $this->escapeIcsString($booking->organizer_name);

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//ROOMS//Meeting Room Booking System//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            "UID:booking-{$booking->reference_code}@rooms-demo.local",
            "DTSTAMP:{$nowUtc}",
            "DTSTART:{$startUtc}",
            "DTEND:{$endUtc}",
            "SUMMARY:{$summary}",
            "DESCRIPTION:{$description}",
            "LOCATION:{$location}",
            "ORGANIZER;CN={$organizerName}:mailto:{$booking->organizer_email}",
            "STATUS:{$status}",
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        return implode("\r\n", $lines) . "\r\n";
    }

    private function escapeIcsString(string $string): string
    {
        $string = str_replace('\\', '\\\\', $string);
        $string = str_replace(';', '\;', $string);
        $string = str_replace(',', '\,', $string);
        $string = str_replace(["\r\n", "\n", "\r"], '\n', $string);

        return $string;
    }
}
