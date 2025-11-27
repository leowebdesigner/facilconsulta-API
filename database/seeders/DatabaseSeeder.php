<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $patients = Patient::factory()->count(10)->create();
        $doctors = Doctor::factory()->count(5)->create();

        $doctors->each(function (Doctor $doctor) use ($patients) {
            $schedules = DoctorSchedule::factory()->count(3)->create([
                'doctor_id' => $doctor->id,
            ]);

            Appointment::factory()->count(3)->create([
                'patient_id' => fn () => $patients->random()->id,
                'doctor_id' => $doctor->id,
                'doctor_schedule_id' => fn () => $schedules->random()->id,
            ]);
        });
    }
}
