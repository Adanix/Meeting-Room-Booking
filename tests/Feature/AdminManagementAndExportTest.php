<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementAndExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_guest_is_redirected_away_from_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_employee_role_receives_403_forbidden_on_admin(): void
    {
        $employee = User::where('role', 'employee')->first();
        $this->assertNotNull($employee);

        $response = $this->actingAs($employee)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_coordinator_and_admin_can_access_admin_panel(): void
    {
        $coordinator = User::where('role', 'coordinator')->first();
        $admin = User::where('role', 'admin')->first();

        $this->actingAs($coordinator)->get('/admin/dashboard')->assertStatus(200);
        $this->actingAs($coordinator)->get('/admin/bookings')->assertStatus(200);

        $this->actingAs($admin)->get('/admin/dashboard')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/rooms')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/settings')->assertStatus(200);
    }

    public function test_csv_export_includes_utf8_bom_and_headers(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get(route('admin.bookings.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        // 1. Must begin with UTF-8 BOM \xEF\xBB\xBF
        $bom = substr($content, 0, 3);
        $this->assertEquals("\xEF\xBB\xBF", $bom, 'CSV must start with UTF-8 BOM for Excel compatibility');

        // 2. Contains CSV headers
        $this->assertStringContainsString('"Reference Code",Status,"Room Name"', $content);
    }

    public function test_admin_can_cancel_and_restore_booking(): void
    {
        $admin = User::where('role', 'admin')->first();
        $booking = Booking::where('status', 'confirmed')->first();
        $this->assertNotNull($booking);

        // Cancel booking
        $cancelResponse = $this->actingAs($admin)->post(route('admin.bookings.cancel', $booking), [
            'reason' => 'Emergency maintenance',
        ]);

        $cancelResponse->assertSessionHasNoErrors();
        $booking->refresh();
        $this->assertEquals('cancelled', $booking->status);
        $this->assertDatabaseHas('booking_status_histories', [
            'booking_id' => $booking->id,
            'action' => 'cancelled',
        ]);

        // Restore booking
        $restoreResponse = $this->actingAs($admin)->post(route('admin.bookings.restore', $booking));
        $restoreResponse->assertSessionHasNoErrors();
        $booking->refresh();
        $this->assertEquals('confirmed', $booking->status);
        $this->assertDatabaseHas('booking_status_histories', [
            'booking_id' => $booking->id,
            'action' => 'restored',
        ]);
    }

    public function test_admin_can_toggle_room_portal_visibility(): void
    {
        $admin = User::where('role', 'admin')->first();
        $room = Room::where('is_active', true)->first();
        $this->assertNotNull($room);

        // Toggle to hidden
        $this->actingAs($admin)->patch(route('admin.rooms.toggle', $room))->assertSessionHasNoErrors();
        $room->refresh();
        $this->assertFalse($room->is_active);

        // Toggle back to active
        $this->actingAs($admin)->patch(route('admin.rooms.toggle', $room))->assertSessionHasNoErrors();
        $room->refresh();
        $this->assertTrue($room->is_active);
    }
}
