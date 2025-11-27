<?php

namespace App\Services;

use App\Models\Appointment;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Repositories\Contracts\DoctorScheduleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    public function __construct(
        private readonly AppointmentRepositoryInterface $appointments,
        private readonly DoctorScheduleRepositoryInterface $schedules
    ) {
    }

    public function schedule(array $data): Appointment
    {
        $schedule = $data['doctor_schedule_id']
            ? $this->schedules->findById($data['doctor_schedule_id'])
            : null;

        if ($schedule && $schedule->doctor_id !== (int) $data['doctor_id']) {
            throw ValidationException::withMessages([
                'doctor_schedule_id' => __('validation.custom.invalid_schedule'),
            ]);
        }

        return $this->appointments->create($data);
    }

    public function listForDoctor(int $doctorId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->appointments->listForDoctor($doctorId, $filters, $perPage);
    }

    public function listForPatient(int $patientId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->appointments->listForPatient($patientId, $filters, $perPage);
    }

    public function updateStatus(Appointment $appointment, string $status, ?string $notes = null): Appointment
    {
        $attributes = ['status' => $status];

        if ($status === Appointment::STATUS_COMPLETED) {
            $attributes['completed_at'] = now();
        }

        if ($status === Appointment::STATUS_CANCELED) {
            $attributes['canceled_at'] = now();
            $attributes['notes'] = $notes ?? $appointment->notes;
        }

        return $this->appointments->update($appointment, $attributes);
    }
}
