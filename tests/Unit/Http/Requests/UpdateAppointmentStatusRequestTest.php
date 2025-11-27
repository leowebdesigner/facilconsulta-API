<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\Appointment\UpdateAppointmentStatusRequest;
use App\Models\Appointment;
use Illuminate\Validation\Rule;
use Tests\TestCase;

class UpdateAppointmentStatusRequestTest extends TestCase
{
    public function test_rules(): void
    {
        $request = new UpdateAppointmentStatusRequest();

        $this->assertEquals([
            'status' => [
                'required',
                Rule::in([
                    Appointment::STATUS_SCHEDULED,
                    Appointment::STATUS_CONFIRMED,
                    Appointment::STATUS_COMPLETED,
                    Appointment::STATUS_CANCELED,
                ]),
            ],
            'notes' => ['nullable', 'string', 'max:500'],
        ], $request->rules());
    }
}
