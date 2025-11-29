<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Repositories\Contracts\DoctorScheduleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    public function __construct(
        private readonly AppointmentRepositoryInterface $appointments,
        private readonly DoctorScheduleRepositoryInterface $schedules
    ) {
    }

    public function schedule(array $data): Appointment
    {
        $doctorId = (int) $data['doctor_id'];
        $scheduleId = (int) $data['doctor_schedule_id'];
        $schedule = $this->schedules->findById($scheduleId);

        if (! $schedule || $schedule->doctor_id !== $doctorId) {
            throw ValidationException::withMessages([
                'doctor_schedule_id' => __('validation.custom.invalid_schedule'),
            ]);
        }

        if (! $schedule->is_active) {
            throw ValidationException::withMessages([
                'doctor_schedule_id' => __('validation.custom.schedule_inactive'),
            ]);
        }

        $scheduledDate = Carbon::parse($data['scheduled_date']);
        $scheduledTime = Carbon::createFromFormat('H:i', $data['scheduled_time']);

        if ((int) $schedule->weekday !== $scheduledDate->dayOfWeekIso) {
            throw ValidationException::withMessages([
                'scheduled_date' => __('validation.custom.schedule_weekday_mismatch'),
            ]);
        }

        if (! $this->timeWithinSchedule($schedule, $scheduledTime)) {
            throw ValidationException::withMessages([
                'scheduled_time' => __('validation.custom.schedule_time_outside_range'),
            ]);
        }

        if (! $this->matchesSlotDuration($schedule, $scheduledTime)) {
            throw ValidationException::withMessages([
                'scheduled_time' => __('validation.custom.schedule_slot_mismatch'),
            ]);
        }

        if ($this->appointments->existsForDoctorAt(
            $doctorId,
            $scheduledDate->toDateString(),
            $scheduledTime->format('H:i')
        )) {
            throw ValidationException::withMessages([
                'scheduled_time' => __('validation.custom.schedule_slot_unavailable'),
            ]);
        }

        $data['scheduled_date'] = $scheduledDate->toDateString();
        $data['scheduled_time'] = $scheduledTime->format('H:i');

        return $this->appointments->create($data);
    }

    public function listForDoctor(int $doctorId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->appointments->listForDoctor($doctorId, $filters, $perPage);
    }

    public function listForPatient(int $patientId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->appointments->listForPatient($patientId, $filters, $perPage);
    }

    public function updateStatus(Appointment $appointment, string $status, ?string $notes = null): Appointment
    {
        $attributes = ['status' => $status];

        if ($status === Appointment::STATUS_COMPLETED) {
            $attributes['completed_at'] = now();
        }

        if ($status === Appointment::STATUS_CANCELED) {
            $attributes['canceled_at'] = now();
            $attributes['notes'] = $notes ?? $appointment->notes;
        }

        return $this->appointments->update($appointment, $attributes);
}

    private function timeWithinSchedule(DoctorSchedule $schedule, Carbon $scheduledTime): bool
    {
        $start = Carbon::parse($schedule->start_time);
        $end = Carbon::parse($schedule->end_time);
        $lastSlotStart = $end->copy()->subMinutes(max(5, (int) $schedule->slot_duration));

        return $scheduledTime->betweenIncluded($start, $lastSlotStart);
    }

    private function matchesSlotDuration(DoctorSchedule $schedule, Carbon $scheduledTime): bool
    {
        $start = Carbon::parse($schedule->start_time);
        $diff = $start->diffInMinutes($scheduledTime);

        return $diff % max(5, (int) $schedule->slot_duration) === 0;
    }
}
