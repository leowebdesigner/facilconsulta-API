<?php

namespace App\Services;

use App\Repositories\Contracts\AppointmentRepositoryInterface;
use App\Repositories\Contracts\PatientRepositoryInterface;
use Illuminate\Support\Collection;

class PatientDashboardService
{
    public function __construct(
        private readonly AppointmentRepositoryInterface $appointments,
        private readonly PatientRepositoryInterface $patients
    ) {
    }

    public function upcomingAppointments(int $patientId, int $limit = 5): Collection
    {
        $paginator = $this->appointments->listForPatient($patientId, [], $limit);

        return collect($paginator->items());
    }

    public function profile(int $patientId): ?\App\Models\Patient
    {
        return $this->patients->findById($patientId);
    }
}
