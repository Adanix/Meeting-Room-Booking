<?php

namespace Tests\Feature;

use App\Models\Room;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomStatusAndSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_room_portal_displays_active_rooms(): void
    {
        $response = $this->get('/rooms');

        $response->assertStatus(200);
        $rooms = $response->viewData('roomsWithStatus')->pluck('room.slug');
        $this->assertTrue($rooms->contains('nikola-tesla-boardroom'));
        $this->assertTrue($rooms->contains('mihajlo-pupin-lab'));
        $this->assertTrue($rooms->contains('danube-quick-sync'));
    }

    public function test_hidden_rooms_are_not_displayed_on_public_portal(): void
    {
        // executive-lounge-vip is seeded with is_active = false
        $response = $this->get('/rooms');

        $response->assertStatus(200);
        $rooms = $response->viewData('roomsWithStatus')->pluck('room.slug');
        $this->assertFalse($rooms->contains('executive-lounge-vip'));

        // Now also hide Tesla Boardroom
        $room = Room::where('slug', 'nikola-tesla-boardroom')->first();
        $this->assertNotNull($room);
        $room->update(['is_active' => false]);

        $responseAfter = $this->get('/rooms');
        $roomsAfter = $responseAfter->viewData('roomsWithStatus')->pluck('room.slug');
        $this->assertFalse($roomsAfter->contains('nikola-tesla-boardroom'));
    }

    public function test_filtering_rooms_by_capacity(): void
    {
        // Nikola Tesla Boardroom has capacity 16, Danube Quick Sync has capacity 6
        $response = $this->get('/rooms?capacity=12');

        $response->assertStatus(200);
        $rooms = $response->viewData('roomsWithStatus')->pluck('room.slug');
        $this->assertTrue($rooms->contains('nikola-tesla-boardroom'));
        // Danube Quick Sync (cap 6) and Avalon (cap 4) should not be in filtered results
        $this->assertFalse($rooms->contains('danube-quick-sync'));
        $this->assertFalse($rooms->contains('avalon-focus-pod-a'));
    }

    public function test_filtering_rooms_by_equipment(): void
    {
        $response = $this->get('/rooms?equipment=catering');

        $response->assertStatus(200);
        $rooms = $response->viewData('roomsWithStatus')->pluck('room.slug');
        $this->assertTrue($rooms->contains('nikola-tesla-boardroom'));
        $this->assertFalse($rooms->contains('danube-quick-sync'));
    }

    public function test_available_slots_api_returns_json_array_of_slots(): void
    {
        $room = Room::where('slug', 'nikola-tesla-boardroom')->first();
        $this->assertNotNull($room);

        $date = now()->next('Tuesday')->toDateString();

        $response = $this->getJson("/api/rooms/{$room->slug}/available-slots?date={$date}&duration=30");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'room' => ['id', 'slug', 'name', 'capacity'],
            'date',
            'is_holiday',
            'is_closed',
            'slots',
        ]);
    }
}
