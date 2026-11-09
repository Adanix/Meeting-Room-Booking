<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\SimulatedEmail;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;

class SimulatedEmailDeliveryService
{
    public function __construct(
        protected SettingService $settingService,
        protected CalendarLinkService $calendarLinkService
    ) {}

    public function sendBookingCreated(Booking $booking): void
    {
        $this->dispatchEmails($booking, 'booking_created');
    }

    public function sendBookingMoved(Booking $booking): void
    {
        $this->dispatchEmails($booking, 'booking_moved');
    }

    public function sendBookingCancelled(Booking $booking): void
    {
        $this->dispatchEmails($booking, 'booking_cancelled');
    }

    public function sendBookingRestored(Booking $booking): void
    {
        $this->dispatchEmails($booking, 'booking_restored');
    }

    protected function dispatchEmails(Booking $booking, string $eventType): void
    {
        $locale = $booking->locale ?: app()->getLocale();
        $officeEmail = $this->settingService->officeEmail();
        $manageUrl = route('bookings.manage', $booking->management_token);
        $icsUrl = route('bookings.ics', $booking->reference_code);
        $googleCalUrl = $this->calendarLinkService->googleCalendarUrl($booking);

        // 1. Employee Email
        $employeeSubject = $this->resolveSubject($eventType, $booking, $locale, 'organizer');
        $employeeHtml = $this->renderHtmlBody($eventType, $booking, $locale, 'organizer', $manageUrl, $icsUrl, $googleCalUrl);
        $employeeText = $this->renderTextBody($eventType, $booking, $locale, 'organizer', $manageUrl, $icsUrl);

        SimulatedEmail::create([
            'booking_id' => $booking->id,
            'event_type' => $eventType,
            'recipient_type' => 'organizer',
            'recipient_email' => $booking->organizer_email,
            'recipient_name' => $booking->organizer_name,
            'subject' => $employeeSubject,
            'locale' => $locale,
            'body_html' => $employeeHtml,
            'body_text' => $employeeText,
            'manage_url' => $manageUrl,
            'ics_url' => $icsUrl,
            'google_calendar_url' => $googleCalUrl,
            'sent_at' => Carbon::now(),
        ]);

        // 2. Central Office Email
        $officeSubject = $this->resolveSubject($eventType, $booking, $locale, 'office');
        $officeHtml = $this->renderHtmlBody($eventType, $booking, $locale, 'office', $manageUrl, $icsUrl, $googleCalUrl);
        $officeText = $this->renderTextBody($eventType, $booking, $locale, 'office', $manageUrl, $icsUrl);

        SimulatedEmail::create([
            'booking_id' => $booking->id,
            'event_type' => $eventType,
            'recipient_type' => 'office',
            'recipient_email' => $officeEmail,
            'recipient_name' => 'Facilities & Office Operations',
            'subject' => $officeSubject,
            'locale' => $locale,
            'body_html' => $officeHtml,
            'body_text' => $officeText,
            'manage_url' => $manageUrl,
            'ics_url' => $icsUrl,
            'google_calendar_url' => $googleCalUrl,
            'sent_at' => Carbon::now(),
        ]);
    }

    protected function resolveSubject(string $eventType, Booking $booking, string $locale, string $recipientType): string
    {
        $roomName = $booking->room ? $booking->room->localizedName($locale) : 'Meeting Room';
        $ref = $booking->reference_code;

        if ($locale === 'sr_Latn') {
            return match ($eventType) {
                'booking_created' => $recipientType === 'organizer'
                    ? "Potvrda rezervacije: {$booking->title} [{$ref}]"
                    : "Nova rezervacija sale: {$roomName} od strane {$booking->organizer_name} [{$ref}]",
                'booking_moved' => "Izmena termina rezervacije: {$booking->title} [{$ref}]",
                'booking_cancelled' => "Otkazivanje rezervacije: {$booking->title} [{$ref}]",
                'booking_restored' => "Vraćena rezervacija: {$booking->title} [{$ref}]",
            };
        }

        return match ($eventType) {
            'booking_created' => $recipientType === 'organizer'
                ? "Booking Confirmation: {$booking->title} [{$ref}]"
                : "New Room Reservation: {$roomName} by {$booking->organizer_name} [{$ref}]",
            'booking_moved' => "Reservation Rescheduled: {$booking->title} [{$ref}]",
            'booking_cancelled' => "Reservation Cancelled: {$booking->title} [{$ref}]",
            'booking_restored' => "Reservation Restored: {$booking->title} [{$ref}]",
        };
    }

    protected function renderHtmlBody(string $eventType, Booking $booking, string $locale, string $recipientType, string $manageUrl, string $icsUrl, string $googleCalUrl): string
    {
        if (View::exists('emails.simulated_template')) {
            return View::make('emails.simulated_template', compact('eventType', 'booking', 'locale', 'recipientType', 'manageUrl', 'icsUrl', 'googleCalUrl'))->render();
        }

        // Inline HTML Fallback
        $isSr = $locale === 'sr_Latn';
        $roomName = $booking->room ? $booking->room->localizedName($locale) : 'Room';
        $location = $booking->room ? $booking->room->location : 'Office';
        $dateStr = $booking->booking_date->format('Y-m-d');
        $timeStr = $booking->timeRangeString();

        $headline = $isSr ? 'Obaveštenje o rezervaciji sale' : 'Meeting Room Booking Notice';
        $greeting = $recipientType === 'organizer'
            ? ($isSr ? "Zdravo, {$booking->organizer_name}" : "Hello {$booking->organizer_name},")
            : ($isSr ? 'Poštovani timu kancelarije,' : 'Dear Facilities Team,');

        return <<<HTML
<div style="font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 24px; border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff;">
    <div style="border-bottom: 2px solid #4f46e5; padding-bottom: 12px; margin-bottom: 20px;">
        <h2 style="color: #0f172a; margin: 0; font-size: 20px;">ROOMS — {$headline}</h2>
        <p style="color: #64748b; font-size: 13px; margin: 4px 0 0 0;">Reference: <strong>{$booking->reference_code}</strong></p>
    </div>
    <p style="color: #334155; font-size: 15px; margin-bottom: 16px;">{$greeting}</p>
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 20px;">
        <table style="width: 100%; border-collapse: collapse; font-size: 14px; color: #334155;">
            <tr><td style="padding: 6px 0; font-weight: bold; width: 140px;">Meeting:</td><td>{$booking->title}</td></tr>
            <tr><td style="padding: 6px 0; font-weight: bold;">Room:</td><td>{$roomName} ({$location})</td></tr>
            <tr><td style="padding: 6px 0; font-weight: bold;">Date & Time:</td><td>{$dateStr} | {$timeStr} ({$booking->duration_minutes} min)</td></tr>
            <tr><td style="padding: 6px 0; font-weight: bold;">Attendees:</td><td>{$booking->attendees_count} people</td></tr>
            <tr><td style="padding: 6px 0; font-weight: bold;">Organizer:</td><td>{$booking->organizer_name} ({$booking->organizer_email})</td></tr>
        </table>
    </div>
    <div style="margin-bottom: 24px;">
        <a href="{$googleCalUrl}" target="_blank" style="display: inline-block; padding: 10px 18px; font-size: 13px; font-weight: bold; color: #ffffff; background: #4f46e5; text-decoration: none; border-radius: 6px; margin-right: 8px;">Add to Google Calendar</a>
        <a href="{$icsUrl}" style="display: inline-block; padding: 10px 18px; font-size: 13px; font-weight: bold; color: #334155; background: #f1f5f9; text-decoration: none; border-radius: 6px;">Download .ics</a>
    </div>
    <div style="border-top: 1px solid #e2e8f0; padding-top: 16px;">
        <p style="font-size: 13px; color: #64748b; margin: 0 0 8px 0;">Need to change or cancel this booking before the meeting starts?</p>
        <a href="{$manageUrl}" style="color: #4f46e5; font-size: 13px; font-weight: bold;">Change or Cancel Booking Link &rarr;</a>
    </div>
</div>
HTML;
    }

    protected function renderTextBody(string $eventType, Booking $booking, string $locale, string $recipientType, string $manageUrl, string $icsUrl): string
    {
        $roomName = $booking->room ? $booking->room->localizedName($locale) : 'Room';
        $location = $booking->room ? $booking->room->location : 'Office';
        $dateStr = $booking->booking_date->format('Y-m-d');
        $timeStr = $booking->timeRangeString();

        return <<<TEXT
ROOMS — Booking Notification
Reference: {$booking->reference_code}
Meeting: {$booking->title}
Room: {$roomName} ({$location})
Date: {$dateStr}
Time: {$timeStr} ({$booking->duration_minutes} mins)
Organizer: {$booking->organizer_name} ({$booking->organizer_email})
Attendees: {$booking->attendees_count}

Download .ics: {$icsUrl}
Manage / Reschedule / Cancel: {$manageUrl}
TEXT;
    }
}
