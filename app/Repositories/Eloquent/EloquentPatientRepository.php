<?php

namespace App\Repositories\Eloquent;

use App\Models\Patient;
use App\Repositories\Contracts\PatientRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentPatientRepository implements PatientRepositoryInterface
{
    public function create(array $attributes): Patient
    {
        return Patient::create($attributes);
    }

    public function update(Patient $patient, array $attributes): Patient
    {
        $patient->update($attributes);

        return $patient;
    }

    public function findById(int $id): ?Patient
    {
        return Patient::find($id);
    }

    public function findByEmail(string $email): ?Patient
    {
        return Patient::email($email)->first();
    }

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Patient::query()
            ->when($filters['search'] ?? null, fn ($query, $term) => $query->search($term))
            ->isActive($filters['active'] ?? null)
            ->paginate($perPage);
    }

    public function allActive(): Collection
    {
        return Patient::active()->get();
    }
}
