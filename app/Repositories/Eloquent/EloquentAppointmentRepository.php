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
            ->with(['doctor', 'patient', 'schedule'])
            ->forDoctor($doctorId)
            ->status($filters['status'] ?? null)
            ->upcoming()
            ->paginate($perPage);
    }

    public function listForPatient(int $patientId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Appointment::query()
            ->with(['doctor', 'patient', 'schedule'])
            ->forPatient($patientId)
            ->status($filters['status'] ?? null)
            ->when(
                (bool) ($filters['upcoming'] ?? false),
                fn ($query) => $query
                    ->whereDate('scheduled_date', '>=', now()->toDateString())
                    ->orderBy('scheduled_date')
                    ->orderBy('scheduled_time'),
                fn ($query) => $query
                    ->orderByDesc('scheduled_date')
                    ->orderByDesc('scheduled_time')
            )
            ->paginate($perPage);
    }

    public function existsForDoctorAt(int $doctorId, string $date, string $time, ?int $ignoreAppointmentId = null): bool
    {
        return Appointment::query()
            ->where('doctor_id', $doctorId)
            ->whereDate('scheduled_date', $date)
            ->whereTime('scheduled_time', $time)
            ->whereIn('status', [
                Appointment::STATUS_SCHEDULED,
                Appointment::STATUS_CONFIRMED,
            ])
            ->when($ignoreAppointmentId, fn ($query) => $query->where('id', '!=', $ignoreAppointmentId))
            ->exists();
    }
}
