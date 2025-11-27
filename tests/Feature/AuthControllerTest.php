<?php

namespace Tests\Feature;

use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_register(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.patient.email', $payload['email']);

        $this->assertDatabaseHas('patients', [
            'email' => $payload['email'],
        ]);
    }

    public function test_patient_can_login_and_receive_token(): void
    {
        $patient = Patient::factory()->create([
            'email' => 'jane@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $patient->email,
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['data' => ['token']]);
    }
}
