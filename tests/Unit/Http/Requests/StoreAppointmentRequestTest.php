<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\Appointment\StoreAppointmentRequest;
use Tests\TestCase;

class StoreAppointmentRequestTest extends TestCase
{
    public function test_rules(): void
    {
        $request = new StoreAppointmentRequest();

        $this->assertSame([
            'doctor_id' => ['required', 'exists:doctors,id'],
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_schedule_id' => ['nullable', 'exists:doctor_schedules,id'],
            'scheduled_date' => ['required', 'date', 'after_or_equal:today'],
            'scheduled_time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], $request->rules());
    }
}
