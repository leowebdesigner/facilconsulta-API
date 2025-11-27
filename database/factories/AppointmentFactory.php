<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            'doctor_id' => Doctor::factory(),
            'doctor_schedule_id' => DoctorSchedule::factory(),
            'scheduled_date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'scheduled_time' => fake()->time('H:i'),
            'status' => Appointment::STATUS_SCHEDULED,
            'notes' => fake()->sentence(),
        ];
    }
}
