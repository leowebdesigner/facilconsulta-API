<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\Auth\LoginPatientRequest;
use Tests\TestCase;

class LoginPatientRequestTest extends TestCase
{
    public function test_rules(): void
    {
        $request = new LoginPatientRequest();

        $this->assertSame([
            'email' => ['required', 'string', 'email', 'exists:patients,email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:120'],
        ], $request->rules());
    }
}
