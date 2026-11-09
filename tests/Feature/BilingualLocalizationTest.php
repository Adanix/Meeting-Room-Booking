<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\SimulatedEmail;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BilingualLocalizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_switching_locale_via_post_endpoint(): void
    {
        // Switch to Serbian Latin
        $responseSr = $this->post(route('locale.switch'), ['locale' => 'sr_Latn']);
        $responseSr->assertSessionHas('locale', 'sr_Latn');

        // Switch to English
        $responseEn = $this->post(route('locale.switch'), ['locale' => 'en']);
        $responseEn->assertSessionHas('locale', 'en');

        // Invalid locale is rejected
        $responseInvalid = $this->post(route('locale.switch'), ['locale' => 'fr']);
        $responseInvalid->assertSessionHasErrors(['locale']);
    }

    public function test_serbian_latin_renders_on_rooms_portal(): void
    {
        $response = $this->withSession(['locale' => 'sr_Latn'])->get('/rooms');

        $response->assertStatus(200);
        $response->assertSee('Sale uživo');
        $response->assertSee('Sala Nikola Tesla');
        $response->assertSee('Dnevni raspored');
    }

    public function test_english_renders_on_rooms_portal(): void
    {
        $response = $this->withSession(['locale' => 'en'])->get('/rooms');

        $response->assertStatus(200);
        $response->assertSee('Live Rooms');
        $response->assertSee('Nikola Tesla Boardroom');
        $response->assertSee('Daily Schedule');
    }

    public function test_bilingual_simulated_email_dispatch(): void
    {
        $room = Room::where('slug', 'nikola-tesla-boardroom')->first();
        $futureDate = now()->addWeeks(2)->next('Tuesday')->toDateString();

        // 1. Create booking in Serbian
        $this->withSession(['locale' => 'sr_Latn'])->post('/bookings', [
            'room_id' => $room->id,
            'booking_date' => $futureDate,
            'start_time' => '10:00',
            'duration_minutes' => 60,
            'title' => 'Strategijski sastanak',
            'organizer_name' => 'Jovan Jovanović',
            'organizer_email' => 'jovan@example.com',
            'attendees_count' => 5,
        ])->assertSessionHasNoErrors();

        $booking = Booking::where('title', 'Strategijski sastanak')->first();
        $this->assertNotNull($booking);
        $this->assertEquals('sr_Latn', $booking->locale);

        // Check simulated email subject and body in Serbian
        $email = SimulatedEmail::where('booking_id', $booking->id)
            ->where('recipient_type', 'organizer')
            ->first();

        $this->assertNotNull($email);
        $this->assertStringContainsString('Potvrda rezervacije: Strategijski sastanak', $email->subject);
        $this->assertStringContainsString('Zdravo, Jovan Jovanović', $email->body_html);
    }
}
