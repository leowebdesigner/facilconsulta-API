<?php

namespace App\Repositories\Contracts;

use App\Models\Patient;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PatientRepositoryInterface
{
    public function create(array $attributes): Patient;

    public function update(Patient $patient, array $attributes): Patient;

    public function findById(int $id): ?Patient;

    public function findByEmail(string $email): ?Patient;

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function allActive(): Collection;
}
