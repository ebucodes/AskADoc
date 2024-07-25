<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;

class AuthTest extends TestCase
{
    #[Test]
    public function it_can_register_a_user()
    {
        $email = 'john' . Str::random(5) . '@example.com';
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => $email, // Ensure unique email
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'access_token',
                'token_type',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);
    }

    #[Test]
    public function it_can_login_a_user()
    {
        $email = 'john' . Str::random(5) . '@example.com';
        $user = User::factory()->create([
            'email' => $email,
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
            ]);
    }

    #[Test]
    public function it_cannot_login_with_invalid_credentials()
    {
        $email = 'john' . Str::random(5) . '@example.com';
        $user = User::factory()->create([
            'email' => $email,
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $email,
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['email'],
            ]);
    }
}