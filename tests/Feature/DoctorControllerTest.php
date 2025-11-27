<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_doctors(): void
    {
        Doctor::factory()->count(2)->create();

        $response = $this->getJson('/api/v1/doctors');

        $response->assertOk()
            ->assertJsonStructure(['data' => ['doctors']]);
    }

    public function test_can_list_available_doctors(): void
    {
        $doctor = Doctor::factory()->create(['specialty' => 'Cardiology']);
        DoctorSchedule::factory()->create([
            'doctor_id' => $doctor->id,
            'weekday' => now()->dayOfWeekIso,
        ]);

        $response = $this->getJson('/api/v1/doctors/available?date='.now()->toDateString());

        $response->assertOk()
            ->assertJsonCount(1, 'data.doctors');
    }
}
