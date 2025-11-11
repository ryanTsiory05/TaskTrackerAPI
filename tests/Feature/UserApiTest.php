<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function new_user_can_register_with_valid_data()
    {
        $userData = [
            'name' => 'Jean Dupont',
            'email' => 'jean.dupont@test.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(201) 
                 ->assertJsonStructure(['access_token']); 

        $this->assertDatabaseHas('users', [
            'email' => 'jean.dupont@test.com',
            'name' => 'Jean Dupont',
        ]);
    }
    
    /** @test */
    public function registration_fails_with_invalid_email()
    {
        $userData = [
            'name' => 'Jean Dupont',
            'email' => 'email-non-valide',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $response = $this->postJson('/api/auth/register', $userData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }
    
/** @test */
    public function user_can_login_with_valid_credentials()
    {
        User::factory()->create([
            'email' => 'user@test.com',
        ]);

        $loginData = [
            'email' => 'user@test.com',
            'password' => 'password',
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token']);
    }

/** @test */
    public function login_fails_with_incorrect_password()
    {
        User::factory()->create([
            'email' => 'user@test.com',
        ]);

        $loginData = [
            'email' => 'user@test.com',
            'password' => 'incorrect_password',
        ];

        $response = $this->postJson('/api/auth/login', $loginData);

        $response->assertStatus(401) 
                 ->assertJsonFragment(['message' => 'Invalid credentials']); 
    }
}