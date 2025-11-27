<?php

namespace Database\Factories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'phone' => fake()->phoneNumber(),
            'document' => fake()->unique()->numerify('###########'),
            'crm' => 'CRM'.fake()->unique()->numerify('#####'),
            'specialty' => fake()->randomElement(['Cardiology', 'Dermatology', 'Pediatrics']),
            'experience_years' => fake()->numberBetween(1, 25),
            'bio' => fake()->sentence(),
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }
}
