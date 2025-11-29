<?php

namespace App\Repositories\Eloquent;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Repositories\Contracts\DoctorRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as SupportCollection;

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

    public function listAvailable(string $date, ?string $specialty = null, int $days = 5): Collection
    {
        $startDate = Carbon::parse($date)->startOfDay();
        $today = Carbon::now()->startOfDay();

        if ($startDate->lt($today)) {
            $startDate = $today->copy();
        }

        $days = max(1, min(180, $days));
        $dateRange = collect(range(0, $days - 1))->map(fn (int $offset) => $startDate->copy()->addDays($offset));
        $weekdays = $dateRange->map->dayOfWeekIso->unique()->values()->all();

        $doctors = Doctor::active()
            ->when($specialty, fn ($query, $value) => $query->bySpecialty($value))
            ->whereHas('schedules', fn ($query) => $query->active()->whereIn('weekday', $weekdays))
            ->with([
                'schedules' => fn ($query) => $query->active()->whereIn('weekday', $weekdays),
                'appointments' => fn ($query) => $query
                    ->whereBetween('scheduled_date', [
                        $dateRange->first()->toDateString(),
                        $dateRange->last()->toDateString(),
                    ])
                    ->whereIn('status', [
                        Appointment::STATUS_SCHEDULED,
                        Appointment::STATUS_CONFIRMED,
                    ]),
            ])->get();

        return $doctors->filter(function (Doctor $doctor) use ($dateRange) {
            $availability = $this->buildAvailability($doctor, $dateRange);
            $doctor->setAttribute('availability', $availability);

            return collect($availability)->contains(fn (array $day) => ! empty($day['slots']));
        })->values();
    }

    private function buildAvailability(Doctor $doctor, SupportCollection $dates): array
    {
        return $dates->map(function (Carbon $date) use ($doctor) {
            $schedule = $doctor->schedules->firstWhere('weekday', $date->dayOfWeekIso);

            if (! $schedule) {
                return [
                    'date' => $date->toDateString(),
                    'weekday' => $date->dayOfWeekIso,
                    'slots' => [],
                ];
            }

            $occupiedSlots = $doctor->appointments
                ->filter(fn ($appointment) => optional($appointment->scheduled_date)->isSameDay($date))
                ->map(fn ($appointment) => optional($appointment->scheduled_time)->format('H:i'))
                ->filter()
                ->values()
                ->all();

            return [
                'date' => $date->toDateString(),
                'weekday' => $date->dayOfWeekIso,
                'slots' => $this->generateSlots($schedule, $occupiedSlots),
            ];
        })->all();
    }

    private function generateSlots(DoctorSchedule $schedule, array $occupiedSlots): array
    {
        $slots = [];
        $start = Carbon::createFromFormat('H:i', $this->formatTime($schedule->start_time));
        $end = Carbon::createFromFormat('H:i', $this->formatTime($schedule->end_time));
        $duration = max(5, (int) $schedule->slot_duration);

        while ($start->lt($end)) {
            $slot = $start->format('H:i');

            if (! in_array($slot, $occupiedSlots, true)) {
                $slots[] = $slot;
            }

            $start->addMinutes($duration);
        }

        return $slots;
    }

    private function formatTime($time): string
    {
        if ($time instanceof Carbon) {
            return $time->format('H:i');
        }

        return Carbon::parse($time)->format('H:i');
    }
}
