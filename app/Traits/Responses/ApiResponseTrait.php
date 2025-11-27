<?php

namespace App\Traits\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

trait ApiResponseTrait
{
    protected function successResponse(array $data = [], int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'timestamp' => Carbon::now()->toIso8601String(),
            'data' => $data,
        ], $status);
    }

    protected function createdResponse(array $data = []): JsonResponse
    {
        return $this->successResponse($data, Response::HTTP_CREATED);
    }

    protected function errorResponse(string $message, int $status = Response::HTTP_BAD_REQUEST, array $errors = []): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'timestamp' => Carbon::now()->toIso8601String(),
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    protected function notFoundResponse(string $message = 'Resource not found.'): JsonResponse
    {
        return $this->errorResponse($message, Response::HTTP_NOT_FOUND);
    }
}
