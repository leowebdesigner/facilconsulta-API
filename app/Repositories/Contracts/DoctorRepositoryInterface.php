<?php

namespace App\Repositories\Contracts;

use App\Models\Doctor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface DoctorRepositoryInterface
{
    public function create(array $attributes): Doctor;

    public function update(Doctor $doctor, array $attributes): Doctor;

    public function findById(int $id): ?Doctor;

    public function findByCrm(string $crm): ?Doctor;

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function listAvailable(string $date, ?string $specialty = null): Collection;
}
