<?php

namespace App\Traits\Http;

use App\Traits\Responses\ApiResponseTrait;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

trait HandlesExceptionsTrait
{
    use ApiResponseTrait;

    protected function safeCall(callable $callback, string $errorMessage = 'Unexpected error occurred.'): JsonResponse
    {
        try {
            return $callback();
        } catch (Throwable $exception) {
            if ($exception instanceof ValidationException
                || $exception instanceof AuthenticationException
                || $exception instanceof AuthorizationException
                || $exception instanceof ModelNotFoundException
                || $exception instanceof HttpExceptionInterface) {
                throw $exception;
            }

            Log::error('Unhandled exception', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return $this->errorResponse($errorMessage, errors: [
                'exception' => config('app.debug') ? $exception->getMessage() : null,
            ]);
        }
    }
}
