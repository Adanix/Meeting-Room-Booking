<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Holiday;
use App\Models\Room;
use App\Models\SimulatedEmail;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingCreationAndRulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_booking_creation_succeeds_and_dispatches_emails_and_history(): void
    {
        $room = Room::where('slug', 'nikola-tesla-boardroom')->first();
        $date = now()->next('Monday')->addWeek()->toDateString();

        $payload = [
            'room_id' => $room->id,
            'booking_date' => $date,
            'start_time' => '14:00',
            'duration_minutes' => 60,
            'title' => 'Product Roadmap Alignment',
            'agenda' => 'Review Q4 priorities and UX audits',
            'organizer_name' => 'Petar Peric',
            'organizer_email' => 'petar.peric@example.com',
            'attendees_count' => 6,
        ];

        $response = $this->post('/bookings', $payload);
        $response->assertSessionHasNoErrors();

        $booking = Booking::where('title', 'Product Roadmap Alignment')->first();
        $this->assertNotNull($booking);
        $this->assertEquals('confirmed', $booking->status);
        $this->assertNotNull($booking->reference_code);
        $this->assertNotNull($booking->management_token);

        $response->assertRedirect(route('bookings.confirmation', $booking->reference_code));

        // Check history
        $this->assertDatabaseHas('booking_status_histories', [
            'booking_id' => $booking->id,
            'action' => 'created',
        ]);

        // Check 2 simulated emails (organizer and office)
        $this->assertEquals(2, SimulatedEmail::where('booking_id', $booking->id)->count());
        $this->assertDatabaseHas('simulated_emails', [
            'booking_id' => $booking->id,
            'recipient_type' => 'organizer',
            'recipient_email' => 'petar.peric@example.com',
        ]);
        $this->assertDatabaseHas('simulated_emails', [
            'booking_id' => $booking->id,
            'recipient_type' => 'office',
        ]);
    }

    public function test_back_to_back_booking_succeeds_without_conflict(): void
    {
        $room = Room::where('slug', 'mihajlo-pupin-lab')->first();
        $date = now()->next('Thursday')->toDateString();

        // 1. Create First booking: 10:00 to 11:00
        $this->post('/bookings', [
            'room_id' => $room->id,
            'booking_date' => $date,
            'start_time' => '10:00',
            'duration_minutes' => 60,
            'title' => 'Session 1',
            'organizer_name' => 'User A',
            'organizer_email' => 'usera@example.com',
            'attendees_count' => 4,
        ])->assertSessionHasNoErrors();

        // 2. Create Second booking immediately adjacent: 11:00 to 12:00
        $response = $this->post('/bookings', [
            'room_id' => $room->id,
            'booking_date' => $date,
            'start_time' => '11:00',
            'duration_minutes' => 60,
            'title' => 'Session 2 Back to Back',
            'organizer_name' => 'User B',
            'organizer_email' => 'userb@example.com',
            'attendees_count' => 4,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('bookings', ['title' => 'Session 2 Back to Back']);
    }

    public function test_overlapping_booking_is_rejected(): void
    {
        $room = Room::where('slug', 'mihajlo-pupin-lab')->first();
        $date = now()->next('Friday')->toDateString();

        // Existing booking: 14:00 to 15:30
        $this->post('/bookings', [
            'room_id' => $room->id,
            'booking_date' => $date,
            'start_time' => '14:00',
            'duration_minutes' => 90,
            'title' => 'Sprint Demo',
            'organizer_name' => 'Lead Dev',
            'organizer_email' => 'lead@example.com',
            'attendees_count' => 5,
        ])->assertSessionHasNoErrors();

        // Attempt overlap 1: 14:30 to 15:30 (inside)
        $responseOverlap1 = $this->post('/bookings', [
            'room_id' => $room->id,
            'booking_date' => $date,
            'start_time' => '14:30',
            'duration_minutes' => 60,
            'title' => 'Conflicting Meeting',
            'organizer_name' => 'Sales Guy',
            'organizer_email' => 'sales@example.com',
            'attendees_count' => 3,
        ]);

        $responseOverlap1->assertSessionHasErrors(['start_time']);
        $this->assertDatabaseMissing('bookings', ['title' => 'Conflicting Meeting']);
    }

    public function test_capacity_exceeded_is_rejected(): void
    {
        $room = Room::where('slug', 'avalon-focus-pod-a')->first(); // Capacity is 4
        $date = now()->next('Tuesday')->toDateString();

        $response = $this->post('/bookings', [
            'room_id' => $room->id,
            'booking_date' => $date,
            'start_time' => '10:00',
            'duration_minutes' => 30,
            'title' => 'Big Team Sync',
            'organizer_name' => 'Manager',
            'organizer_email' => 'manager@example.com',
            'attendees_count' => 10, // Exceeds 4
        ]);

        $response->assertSessionHasErrors(['attendees_count']);
    }

    public function test_booking_on_holiday_is_rejected(): void
    {
        $room = Room::where('slug', 'nikola-tesla-boardroom')->first();
        $holiday = Holiday::first();
        $this->assertNotNull($holiday);

        $response = $this->post('/bookings', [
            'room_id' => $room->id,
            'booking_date' => $holiday->holiday_date->toDateString(),
            'start_time' => '10:00',
            'duration_minutes' => 60,
            'title' => 'Holiday Meeting',
            'organizer_name' => 'Workaholic',
            'organizer_email' => 'work@example.com',
            'attendees_count' => 2,
        ]);

        $response->assertSessionHasErrors(['booking_date']);
    }
}
