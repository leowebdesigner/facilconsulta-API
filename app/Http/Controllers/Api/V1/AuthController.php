<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginPatientRequest;
use App\Http\Requests\Auth\RegisterPatientRequest;
use App\Http\Resources\V1\PatientResource;
use App\Services\AuthService;
use App\Traits\Http\HandlesExceptionsTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    use HandlesExceptionsTrait;

    public function __construct(private readonly AuthService $authService)
    {
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/register",
     *     tags={"Auth"},
     *     summary="Registrar paciente",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Paciente criado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="timestamp", type="string", format="date-time"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="patient", ref="#/components/schemas/Patient")
     *             )
     *         )
     *     )
     * )
     */
    public function register(RegisterPatientRequest $request): JsonResponse
    {
        return $this->safeCall(function () use ($request) {
            $patient = $this->authService->register($request->validated());

            return $this->createdResponse([
                'patient' => new PatientResource($patient),
            ]);
        });
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     tags={"Auth"},
     *     summary="Login paciente",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token retornado",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="timestamp", type="string"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string")
     *             )
     *         )
     *     )
     * )
     */
    public function login(LoginPatientRequest $request): JsonResponse
    {
        return $this->safeCall(function () use ($request) {
            $token = $this->authService->login(
                $request->validated('email'),
                $request->validated('password'),
                $request->validated('device_name')
            );

            return $this->successResponse(['token' => $token]);
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/auth/me",
     *     tags={"Auth"},
     *     summary="Dados do paciente autenticado",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Paciente autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="patient", ref="#/components/schemas/Patient")
     *             )
     *         )
     *     )
     * )
     */
    public function me(Request $request): JsonResponse
    {
        return $this->safeCall(function () use ($request) {
            return $this->successResponse([
                'patient' => new PatientResource($request->user()),
            ]);
        });
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     tags={"Auth"},
     *     summary="Logout e revogação de tokens",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Logout realizado")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        return $this->safeCall(function () use ($request) {
            $this->authService->logout($request->user(), $request->input('token_id'));

            return $this->successResponse(['message' => __('auth.logged_out')]);
        });
    }
}
