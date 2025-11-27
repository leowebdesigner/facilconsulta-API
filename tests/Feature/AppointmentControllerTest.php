<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AppointmentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_schedule_appointment(): void
    {
        $patient = Patient::factory()->create();
        $doctor = Doctor::factory()->create();
        $schedule = DoctorSchedule::factory()->create([
            'doctor_id' => $doctor->id,
        ]);

        Sanctum::actingAs($patient);

        $payload = [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'doctor_schedule_id' => $schedule->id,
            'scheduled_date' => now()->addDay()->toDateString(),
            'scheduled_time' => '10:00',
        ];

        $response = $this->postJson('/api/v1/appointments', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.appointment.patient_id', $patient->id);

        $this->assertDatabaseHas('appointments', [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
        ]);
    }
}
