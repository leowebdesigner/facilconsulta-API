<?php

namespace App\Repositories\Contracts;

use App\Models\DoctorSchedule;
use Illuminate\Database\Eloquent\Collection;

interface DoctorScheduleRepositoryInterface
{
    public function create(array $attributes): DoctorSchedule;

    public function update(DoctorSchedule $schedule, array $attributes): DoctorSchedule;

    public function findById(int $id): ?DoctorSchedule;

    public function forDoctor(int $doctorId, ?int $weekday = null): Collection;
}
