<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\Auth\RegisterPatientRequest;
use Tests\TestCase;

class RegisterPatientRequestTest extends TestCase
{
    public function test_rules(): void
    {
        $request = new RegisterPatientRequest();

        $this->assertSame([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:patients,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'document' => ['nullable', 'string', 'max:50', 'unique:patients,document'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
        ], $request->rules());
    }
}
