<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\DoctorResource;
use App\Services\DoctorService;
use App\Traits\Http\HandlesExceptionsTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class DoctorController extends Controller
{
    use HandlesExceptionsTrait;

    public function __construct(private readonly DoctorService $doctorService)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/doctors",
     *     tags={"Doctors"},
     *     summary="Listar médicos",
     *     @OA\Parameter(name="specialty", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="active", in="query", required=false, @OA\Schema(type="boolean")),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de médicos",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="doctors", type="array", @OA\Items(ref="#/components/schemas/Doctor"))
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        return $this->safeCall(function () use ($request) {
            $doctors = $this->doctorService->list($request->only(['specialty', 'active']), $request->integer('per_page', 15));

            return $this->successResponse([
                'doctors' => DoctorResource::collection($doctors),
            ]);
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/doctors/available",
     *     tags={"Doctors"},
     *     summary="Listar médicos disponíveis por data",
     *     @OA\Parameter(name="date", in="query", @OA\Schema(type="string", format="date")),
     *     @OA\Parameter(name="specialty", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="days", in="query", @OA\Schema(type="integer", minimum=1, maximum=180), description="Quantidade de dias consecutivos a partir da data inicial (até ~6 meses)"),
     *     @OA\Response(
     *         response=200,
     *         description="Médicos disponíveis",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="doctors", type="array", @OA\Items(ref="#/components/schemas/Doctor"))
     *             )
     *         )
     *     )
     * )
     */
    public function available(Request $request): JsonResponse
    {
        return $this->safeCall(function () use ($request) {
            $date = $request->query('date', now()->toDateString());
            $specialty = $request->query('specialty');
            $days = (int) $request->query('days', 5);
            $days = max(1, min(180, $days));
            $doctors = $this->doctorService->listAvailable($date, $specialty, $days);

            return $this->successResponse([
                'doctors' => DoctorResource::collection($doctors),
            ]);
        });
    }
}
