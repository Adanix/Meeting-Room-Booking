<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class P0PublicAndDemoAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_p0_01_homepage_returns_200_and_renders_brand_and_customization_notice(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('ROOMS');
        $response->assertSee('mailto:nixresponding@gmail.com', false);
        $response->assertSee(route('public.docs'));
    }

    public function test_p0_02_docs_page_returns_200_and_contains_customization_notice(): void
    {
        $response = $this->get('/docs');

        $response->assertStatus(200);
        $response->assertSee('mailto:nixresponding@gmail.com', false);
        $response->assertSee('ROOMS Documentation');
    }

    public function test_p0_03_demo_login_page_renders_three_personas(): void
    {
        $response = $this->get('/demo/login');

        $response->assertStatus(200);
        $response->assertSee('admin@rooms-demo.local');
        $response->assertSee('coordinator@rooms-demo.local');
        $response->assertSee('marko.petrovic@rooms-demo.local');
        $response->assertSee('quick-login');
    }

    public function test_p0_04_quick_login_authenticates_each_persona_and_redirects(): void
    {
        // 1. Admin login -> redirects to /admin/dashboard
        $adminResponse = $this->post('/demo/quick-login', ['persona_key' => 'admin']);
        $adminResponse->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
        $this->assertEquals('admin', auth()->user()->role);

        // Logout
        $this->post('/logout');
        $this->assertGuest();

        // 2. Coordinator login -> redirects to /admin/bookings
        $coordResponse = $this->post('/demo/quick-login', ['persona_key' => 'coordinator']);
        $coordResponse->assertRedirect('/admin/bookings');
        $this->assertAuthenticated();
        $this->assertEquals('coordinator', auth()->user()->role);

        // Logout
        $this->post('/logout');
        $this->assertGuest();

        // 3. Employee login -> redirects to /rooms
        $empResponse = $this->post('/demo/quick-login', ['persona_key' => 'employee']);
        $empResponse->assertRedirect('/rooms');
        $this->assertAuthenticated();
        $this->assertEquals('employee', auth()->user()->role);
    }

    public function test_p0_05_logout_invalidates_session_and_redirects(): void
    {
        $user = User::where('role', 'employee')->first();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/demo/login');
        $this->assertGuest();
    }

    public function test_p0_06_demo_routes_return_404_when_demo_mode_disabled(): void
    {
        config(['demo.enabled' => false]);

        $this->get('/demo/login')->assertStatus(404);
        $this->post('/demo/quick-login', ['persona' => 'admin'])->assertStatus(404);
    }
}
