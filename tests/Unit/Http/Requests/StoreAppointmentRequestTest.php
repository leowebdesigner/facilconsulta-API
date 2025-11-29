<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\Appointment\StoreAppointmentRequest;
use Illuminate\Validation\Rule;
use Tests\TestCase;

class StoreAppointmentRequestTest extends TestCase
{
    public function test_rules(): void
    {
        $request = new StoreAppointmentRequest();

        $this->assertEquals([
            'doctor_id' => ['required', 'integer', Rule::exists('doctors', 'id')->whereNull('deleted_at')],
            'patient_id' => [
                'required',
                'integer',
                Rule::exists('patients', 'id')->whereNull('deleted_at'),
                Rule::in([0]),
            ],
            'doctor_schedule_id' => ['required', 'integer', Rule::exists('doctor_schedules', 'id')->whereNull('deleted_at')],
            'scheduled_date' => ['required', 'date', 'after_or_equal:today'],
            'scheduled_time' => ['required', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], $request->rules());
    }
}
