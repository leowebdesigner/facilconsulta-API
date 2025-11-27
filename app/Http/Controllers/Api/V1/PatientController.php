<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AppointmentResource;
use App\Http\Resources\V1\PatientResource;
use App\Services\PatientDashboardService;
use App\Traits\Http\HandlesExceptionsTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    use HandlesExceptionsTrait;

    public function __construct(private readonly PatientDashboardService $dashboardService)
    {
    }

    public function profile(Request $request): JsonResponse
    {
        return $this->safeCall(function () use ($request) {
            return $this->successResponse([
                'patient' => new PatientResource($request->user()),
            ]);
        });
    }

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
