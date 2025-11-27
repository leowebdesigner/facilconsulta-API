<?php

namespace App\Repositories\Eloquent;

use App\Models\Doctor;
use App\Repositories\Contracts\DoctorRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentDoctorRepository implements DoctorRepositoryInterface
{
    public function create(array $attributes): Doctor
    {
        return Doctor::create($attributes);
    }

    public function update(Doctor $doctor, array $attributes): Doctor
    {
        $doctor->update($attributes);

        return $doctor;
    }

    public function findById(int $id): ?Doctor
    {
        return Doctor::find($id);
    }

    public function findByCrm(string $crm): ?Doctor
    {
        return Doctor::where('crm', $crm)->first();
    }

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Doctor::query()
            ->when($filters['specialty'] ?? null, fn ($query, $specialty) => $query->bySpecialty($specialty))
            ->activeState($filters['active'] ?? null)
            ->paginate($perPage);
    }

    public function listAvailable(string $date, ?string $specialty = null): Collection
    {
        $weekday = (int) now()->parse($date)->dayOfWeekIso;

        return Doctor::active()
            ->when($specialty, fn ($query, $value) => $query->bySpecialty($value))
            ->whereHas('schedules', fn ($query) => $query->active()->forWeekday($weekday))
            ->with(['schedules' => fn ($query) => $query->active()->forWeekday($weekday)])
            ->get();
    }
}
