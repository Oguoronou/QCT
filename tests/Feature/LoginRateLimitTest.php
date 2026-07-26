<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_excessive_login_attempts_are_rate_limited(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->post('/login', ['email' => 'nobody@example.com', 'password' => 'wrong']);
        }

        $response = $this->post('/login', ['email' => 'nobody@example.com', 'password' => 'wrong']);

        $response->assertStatus(429);
    }
}
