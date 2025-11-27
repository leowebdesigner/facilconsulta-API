<?php

namespace App\Http\Controllers\Api\V1;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="FacilConsulta API",
 *     description="Documentação oficial da FacilConsulta API"
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Servidor Local"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="Sanctum Token"
 * )
 */
class SwaggerController
{
    // Classe vazia usada apenas para anotações globais do Swagger.
}
