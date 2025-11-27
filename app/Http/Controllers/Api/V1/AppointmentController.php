<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentStatusRequest;
use App\Http\Resources\V1\AppointmentResource;
use App\Models\Appointment;
use App\Services\AppointmentService;
use App\Traits\Http\HandlesExceptionsTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    use HandlesExceptionsTrait;

    public function __construct(private readonly AppointmentService $appointmentService)
    {
    }

    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        return $this->safeCall(function () use ($request) {
            $appointment = $this->appointmentService->schedule($request->validated());

            return $this->createdResponse([
                'appointment' => new AppointmentResource($appointment),
            ]);
        });
    }

    public function doctorAppointments(Request $request, int $doctorId): JsonResponse
    {
        return $this->safeCall(function () use ($request, $doctorId) {
            $appointments = $this->appointmentService->listForDoctor(
                $doctorId,
                $request->only('status'),
                $request->integer('per_page', 15)
            );

            return $this->successResponse([
                'appointments' => AppointmentResource::collection($appointments),
            ]);
        });
    }

    public function patientAppointments(Request $request, int $patientId): JsonResponse
    {
        return $this->safeCall(function () use ($request, $patientId) {
            $appointments = $this->appointmentService->listForPatient(
                $patientId,
                $request->only('status'),
                $request->integer('per_page', 15)
            );

            return $this->successResponse([
                'appointments' => AppointmentResource::collection($appointments),
            ]);
        });
    }

    public function updateStatus(UpdateAppointmentStatusRequest $request, Appointment $appointment): JsonResponse
    {
        return $this->safeCall(function () use ($request, $appointment) {
            $updated = $this->appointmentService->updateStatus(
                $appointment,
                $request->validated('status'),
                $request->validated('notes')
            );

            return $this->successResponse([
                'appointment' => new AppointmentResource($updated),
            ]);
        });
    }
}
