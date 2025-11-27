<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\Http\HandlesExceptionsTrait;
use Illuminate\Http\JsonResponse;

class HealthCheckController extends Controller
{
    use HandlesExceptionsTrait;

    public function __invoke(): JsonResponse
    {
        return $this->safeCall(function () {
            return $this->successResponse([
                'status' => 'ok',
            ]);
        });
    }
}
