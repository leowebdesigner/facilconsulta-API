<?php

namespace Tests\Feature;

use App\Models\Appointment;
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
        $scheduledDate = now()->addDays(2);
        $schedule = DoctorSchedule::factory()->create([
            'doctor_id' => $doctor->id,
            'weekday' => $scheduledDate->dayOfWeekIso,
            'start_time' => '09:00',
            'end_time' => '12:00',
            'slot_duration' => 30,
        ]);

        Sanctum::actingAs($patient);

        $payload = [
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'doctor_schedule_id' => $schedule->id,
            'scheduled_date' => $scheduledDate->toDateString(),
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

    public function test_can_list_patient_appointments_with_doctor_details(): void
    {
        $patient = Patient::factory()->create();
        $doctor = Doctor::factory()->create([
            'name' => 'Dr. Disponivel',
            'specialty' => 'Cardiologia',
            'address' => 'Rua Doc Teste, 500',
        ]);
        $scheduledDate = now()->addDays(3);
        $schedule = DoctorSchedule::factory()->create([
            'doctor_id' => $doctor->id,
            'weekday' => $scheduledDate->dayOfWeekIso,
            'start_time' => '13:00',
            'end_time' => '17:00',
            'slot_duration' => 60,
        ]);

        Appointment::factory()->create([
            'patient_id' => $patient->id,
            'doctor_id' => $doctor->id,
            'doctor_schedule_id' => $schedule->id,
            'scheduled_date' => $scheduledDate->toDateString(),
            'scheduled_time' => '13:00',
        ]);

        Sanctum::actingAs($patient);

        $response = $this->getJson('/api/v1/appointments/patient/'.$patient->id);

        $response->assertOk()
            ->assertJsonPath('data.appointments.0.doctor_name', 'Dr. Disponivel')
            ->assertJsonPath('data.appointments.0.doctor_address', 'Rua Doc Teste, 500')
            ->assertJsonPath('data.appointments.0.doctor_specialty', 'Cardiologia');
    }
}
