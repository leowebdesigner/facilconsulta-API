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
use OpenApi\Annotations as OA;

class AppointmentController extends Controller
{
    use HandlesExceptionsTrait;

    public function __construct(private readonly AppointmentService $appointmentService)
    {
    }

    /**
     * @OA\Post(
     *     path="/api/v1/appointments",
     *     tags={"Appointments"},
     *     summary="Criar agendamento",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"patient_id","doctor_id","scheduled_date","scheduled_time"},
     *             @OA\Property(property="patient_id", type="integer"),
     *             @OA\Property(property="doctor_id", type="integer"),
     *             @OA\Property(property="doctor_schedule_id", type="integer", nullable=true),
     *             @OA\Property(property="scheduled_date", type="string", format="date"),
     *             @OA\Property(property="scheduled_time", type="string", example="10:00"),
     *             @OA\Property(property="notes", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Agendamento criado",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="appointment", ref="#/components/schemas/Appointment")
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        return $this->safeCall(function () use ($request) {
            $appointment = $this->appointmentService->schedule($request->validated());

            return $this->createdResponse([
                'appointment' => new AppointmentResource($appointment),
            ]);
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/appointments/doctor/{doctorId}",
     *     tags={"Appointments"},
     *     summary="Agendamentos por médico",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="doctorId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Lista paginada",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="appointments", type="array", @OA\Items(ref="#/components/schemas/Appointment"))
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/v1/appointments/patient/{patientId}",
     *     tags={"Appointments"},
     *     summary="Agendamentos por paciente",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="patientId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Lista paginada",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="appointments", type="array", @OA\Items(ref="#/components/schemas/Appointment"))
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Patch(
     *     path="/api/v1/appointments/{appointment}/status",
     *     tags={"Appointments"},
     *     summary="Atualizar status do agendamento",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="appointment", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", example="confirmed"),
     *             @OA\Property(property="notes", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Status atualizado")
     * )
     */
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
