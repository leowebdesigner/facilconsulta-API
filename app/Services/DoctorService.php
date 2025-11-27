<?php

namespace App\Services;

use App\Repositories\Contracts\DoctorRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class DoctorService
{
    public function __construct(private readonly DoctorRepositoryInterface $doctors)
    {
    }

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->doctors->list($filters, $perPage);
    }

    public function listAvailable(string $date, ?string $specialty = null): Collection
    {
        return $this->doctors->listAvailable($date, $specialty);
    }
}
