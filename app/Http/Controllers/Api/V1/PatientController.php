<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AppointmentResource;
use App\Http\Resources\V1\PatientResource;
use App\Services\PatientDashboardService;
use App\Traits\Http\HandlesExceptionsTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class PatientController extends Controller
{
    use HandlesExceptionsTrait;

    public function __construct(private readonly PatientDashboardService $dashboardService)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/patient/profile",
     *     tags={"Patients"},
     *     summary="Perfil do paciente autenticado",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Perfil", @OA\JsonContent(
     *         @OA\Property(property="data", type="object",
     *             @OA\Property(property="patient", ref="#/components/schemas/Patient")
     *         )
     *     ))
     * )
     */
    public function profile(Request $request): JsonResponse
    {
        return $this->safeCall(function () use ($request) {
            return $this->successResponse([
                'patient' => new PatientResource($request->user()),
            ]);
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/patient/appointments/upcoming",
     *     tags={"Patients"},
     *     summary="Próximos agendamentos do paciente",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de agendamentos futuros",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="appointments", type="array", @OA\Items(ref="#/components/schemas/Appointment"))
     *             )
     *         )
     *     )
     * )
     */
    public function upcoming(Request $request): JsonResponse
    {
        return $this->safeCall(function () use ($request) {
            $limit = $request->integer('limit', 5);
            $collection = $this->dashboardService->upcomingAppointments($request->user()->id, $limit);

            return $this->successResponse([
                'appointments' => AppointmentResource::collection($collection),
            ]);
        });
    }
}
