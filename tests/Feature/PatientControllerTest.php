<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PatientControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_patient_can_view_upcoming_appointments(): void
    {
        $patient = Patient::factory()->create();
        $doctor = Doctor::factory()->create();
        $schedule = DoctorSchedule::factory()->create([
            'doctor_id' => $doctor->id,
        ]);

        Appointment::factory()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'doctor_schedule_id' => $schedule->id,
            'scheduled_date' => now()->addDays(2)->toDateString(),
        ]);

        Sanctum::actingAs($patient);

        $response = $this->getJson('/api/v1/patient/appointments/upcoming');

        $response->assertOk()
            ->assertJsonCount(1, 'data.appointments');
    }
}
