<?php

namespace Tests\Feature;

use App\Models\Booking;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfirmationAndCalendarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_booking_confirmation_page_renders_details_and_action_links(): void
    {
        $booking = Booking::where('status', 'confirmed')->first();
        $this->assertNotNull($booking);

        $response = $this->get(route('bookings.confirmation', $booking->reference_code));

        $response->assertStatus(200);
        $response->assertSee($booking->reference_code);
        $response->assertSee($booking->title);
        $response->assertSee($booking->room->localized_name);
        // Action links
        $response->assertSee(route('bookings.ics', $booking->reference_code));
        $response->assertSee(route('bookings.manage', $booking->management_token));
        $response->assertSee('calendar.google.com/calendar/render', false);
    }

    public function test_ics_download_returns_valid_rfc5545_calendar(): void
    {
        $booking = Booking::where('status', 'confirmed')->first();
        $this->assertNotNull($booking);

        $response = $this->get(route('bookings.ics', $booking->reference_code));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="booking-' . $booking->reference_code . '.ics"');

        $content = $response->getContent();
        $this->assertStringContainsString('BEGIN:VCALENDAR', $content);
        $this->assertStringContainsString('VERSION:2.0', $content);
        $this->assertStringContainsString('BEGIN:VEVENT', $content);
        $this->assertStringContainsString('SUMMARY:' . $booking->title, $content);
        $this->assertStringContainsString('UID:booking-' . $booking->reference_code . '@rooms-demo.local', $content);
        $this->assertStringContainsString('END:VEVENT', $content);
        $this->assertStringContainsString('END:VCALENDAR', $content);
    }
}
