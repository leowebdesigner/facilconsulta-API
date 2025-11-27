<?php

namespace App\Repositories\Eloquent;

use App\Models\Appointment;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentAppointmentRepository implements AppointmentRepositoryInterface
{
    public function create(array $attributes): Appointment
    {
        return Appointment::create($attributes);
    }

    public function update(Appointment $appointment, array $attributes): Appointment
    {
        $appointment->update($attributes);

        return $appointment;
    }

    public function findById(int $id): ?Appointment
    {
        return Appointment::with(['doctor', 'patient', 'schedule'])->find($id);
    }

    public function listForDoctor(int $doctorId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Appointment::query()
            ->forDoctor($doctorId)
            ->status($filters['status'] ?? null)
            ->upcoming()
            ->paginate($perPage);
    }

    public function listForPatient(int $patientId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Appointment::query()
            ->forPatient($patientId)
            ->status($filters['status'] ?? null)
            ->orderByDesc('scheduled_date')
            ->orderByDesc('scheduled_time')
            ->paginate($perPage);
    }
}
