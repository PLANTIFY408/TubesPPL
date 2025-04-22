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

    public function test_home_page_can_be_accessed()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_consultation_page_requires_authentication()
    {
        // Test akses tanpa login
        $response = $this->get('/consultation');
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        // Test akses dengan login
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/consultation');
        $response->assertStatus(200);
    }

    public function test_products_page_can_be_accessed()
    {
        $response = $this->get('/products');
        $response->assertStatus(200);
    }

    public function test_login_page_can_be_accessed()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_register_page_can_be_accessed()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }
}

