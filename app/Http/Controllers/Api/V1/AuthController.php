<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginPatientRequest;
use App\Http\Requests\Auth/RegisterPatientRequest;
use App\Http\Resources\V1\PatientResource;
use App\Services\AuthService;
use App\Traits\Http\HandlesExceptionsTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use HandlesExceptionsTrait;

    public function __construct(private readonly AuthService $authService)
    {
    }

    public function register(RegisterPatientRequest $request): JsonResponse
    {
        return $this->safeCall(function () use ($request) {
            $patient = $this->authService->register($request->validated());

            return $this->createdResponse([
                'patient' => new PatientResource($patient),
            ]);
        });
    }

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

    public function me(Request $request): JsonResponse
    {
        return $this->safeCall(function () use ($request) {
            return $this->successResponse([
                'patient' => new PatientResource($request->user()),
            ]);
        });
    }

    public function logout(Request $request): JsonResponse
    {
        return $this->safeCall(function () use ($request) {
            $this->authService->logout($request->user(), $request->input('token_id'));

            return $this->successResponse(['message' => __('auth.logged_out')]);
        });
    }
}
