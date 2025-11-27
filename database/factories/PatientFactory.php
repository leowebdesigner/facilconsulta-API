<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'phone' => fake()->phoneNumber(),
            'document' => fake()->unique()->numerify('###########'),
            'birth_date' => fake()->date(),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'is_active' => true,
            'remember_token' => Str::random(10),
        ];
    }
}
