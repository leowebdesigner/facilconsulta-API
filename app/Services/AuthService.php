<?php

namespace App\Services;

use App\Models\Patient;
use App\Repositories\Contracts\PatientRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private readonly PatientRepositoryInterface $patients
    ) {
    }

    public function register(array $data): Patient
    {
        $data['password'] = Hash::make($data['password']);

        return $this->patients->create($data);
    }

    public function login(string $email, string $password, ?string $deviceName = null): string
    {
        $patient = $this->patients->findByEmail($email);

        if (! $patient || ! Hash::check($password, $patient->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $device = $deviceName ?? 'patient-device';

        return $patient->createToken($device)->plainTextToken;
    }

    public function logout(Patient $patient, ?string $tokenId = null): void
    {
        if ($tokenId) {
            $patient->tokens()->where('id', $tokenId)->delete();

            return;
        }

        $patient->tokens()->delete();
    }
}
