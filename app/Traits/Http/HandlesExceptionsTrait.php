<?php

namespace App\Traits\Http;

use App\Traits\Responses\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

trait HandlesExceptionsTrait
{
    use ApiResponseTrait;

    protected function safeCall(callable $callback, string $errorMessage = 'Unexpected error occurred.'): JsonResponse
    {
        try {
            return $callback();
        } catch (Throwable $exception) {
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
