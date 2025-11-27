<?php

namespace App\Repositories\Contracts;

use App\Models\Appointment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AppointmentRepositoryInterface
{
    public function create(array $attributes): Appointment;

    public function update(Appointment $appointment, array $attributes): Appointment;

    public function findById(int $id): ?Appointment;

    public function listForDoctor(int $doctorId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function listForPatient(int $patientId, array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
