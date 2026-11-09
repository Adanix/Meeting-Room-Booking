<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\SimulatedEmail;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SelfServiceManageBookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_self_service_page_renders_for_valid_token(): void
    {
        $booking = Booking::where('status', 'confirmed')->first();
        $this->assertNotNull($booking);

        $response = $this->get(route('bookings.manage', $booking->management_token));

        $response->assertStatus(200);
        $response->assertSee($booking->reference_code);
        $response->assertSee($booking->title);
    }

    public function test_self_service_can_reschedule_future_booking(): void
    {
        $booking = Booking::where('status', 'confirmed')->first();
        $this->assertNotNull($booking);

        // Ensure booking is strictly in the future
        $booking->update([
            'booking_date' => now()->addWeek()->toDateString(),
            'start_at' => now()->addWeek()->setTime(10, 0),
            'end_at' => now()->addWeek()->setTime(11, 0),
        ]);

        // Target date 2 weeks ahead (Monday)
        $futureDate = now()->addWeeks(2)->next('Monday')->toDateString();

        $response = $this->put(route('bookings.manage.update', $booking->management_token), [
            'booking_date' => $futureDate,
            'start_time' => '15:00',
            'duration_minutes' => 60,
        ]);

        $response->assertSessionHasNoErrors();
        $booking->refresh();

        $this->assertEquals($futureDate, $booking->booking_date->toDateString());
        $this->assertEquals('15:00:00', $booking->start_at->toTimeString());

        // History logged
        $this->assertDatabaseHas('booking_status_histories', [
            'booking_id' => $booking->id,
            'action' => 'rescheduled',
        ]);
    }

    public function test_self_service_can_cancel_future_booking(): void
    {
        $booking = Booking::where('status', 'confirmed')->first();
        $this->assertNotNull($booking);

        // Ensure booking is in the future
        $booking->update([
            'booking_date' => now()->addWeek()->toDateString(),
            'start_at' => now()->addWeek()->setTime(10, 0),
            'end_at' => now()->addWeek()->setTime(11, 0),
        ]);

        $response = $this->delete(route('bookings.manage.cancel', $booking->management_token), [
            'reason' => 'Client postponed the session',
        ]);

        $response->assertSessionHasNoErrors();
        $booking->refresh();

        $this->assertEquals('cancelled', $booking->status);
        $this->assertEquals('Client postponed the session', $booking->cancellation_reason);
        $this->assertNotNull($booking->cancelled_at);

        // History record logged
        $this->assertDatabaseHas('booking_status_histories', [
            'booking_id' => $booking->id,
            'action' => 'cancelled',
            'note' => 'Cancelled: Client postponed the session',
        ]);
    }

    public function test_self_service_cutoff_rejects_past_meeting_changes(): void
    {
        $booking = Booking::where('status', 'confirmed')->first();
        $this->assertNotNull($booking);

        // Set booking to yesterday
        $booking->update([
            'booking_date' => now()->subDay()->toDateString(),
            'start_at' => now()->subDay()->setTime(10, 0),
            'end_at' => now()->subDay()->setTime(11, 0),
        ]);

        // Attempt reschedule
        $responseReschedule = $this->put(route('bookings.manage.update', $booking->management_token), [
            'booking_date' => now()->addWeek()->toDateString(),
            'start_time' => '11:00',
            'duration_minutes' => 30,
        ]);

        $responseReschedule->assertSessionHasErrors();
        $booking->refresh();
        $this->assertNotEquals('11:00:00', $booking->start_at->toTimeString());

        // Attempt cancel
        $responseCancel = $this->delete(route('bookings.manage.cancel', $booking->management_token));
        $responseCancel->assertSessionHasErrors();
        $booking->refresh();
        $this->assertEquals('confirmed', $booking->status);
    }
}
