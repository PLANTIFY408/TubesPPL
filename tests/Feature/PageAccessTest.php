<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PageAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_monitoring_page_requires_authentication()
    {
        // Test akses tanpa login
        $response = $this->get('/monitoring');
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        // Test akses dengan login
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/monitoring');
        $response->assertStatus(200);
    }

}
