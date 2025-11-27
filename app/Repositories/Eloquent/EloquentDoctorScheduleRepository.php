<?php

namespace App\Repositories\Eloquent;

use App\Models\DoctorSchedule;
use App\Repositories\Contracts\DoctorScheduleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentDoctorScheduleRepository implements DoctorScheduleRepositoryInterface
{
    public function create(array $attributes): DoctorSchedule
    {
        return DoctorSchedule::create($attributes);
    }

    public function update(DoctorSchedule $schedule, array $attributes): DoctorSchedule
    {
        $schedule->update($attributes);

        return $schedule;
    }

    public function findById(int $id): ?DoctorSchedule
    {
        return DoctorSchedule::find($id);
    }

    public function forDoctor(int $doctorId, ?int $weekday = null): Collection
    {
        return DoctorSchedule::query()
            ->where('doctor_id', $doctorId)
            ->forWeekday($weekday)
            ->active()
            ->get();
    }
}
