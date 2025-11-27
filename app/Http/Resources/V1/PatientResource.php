<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Patient",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", example="john@example.com"),
 *     @OA\Property(property="phone", type="string"),
 *     @OA\Property(property="document", type="string"),
 *     @OA\Property(property="birth_date", type="string", format="date"),
 *     @OA\Property(property="gender", type="string", example="male"),
 *     @OA\Property(property="is_active", type="boolean"),
 *     @OA\Property(property="created_at", type="string", format="date-time")
 * )
 */
class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'document' => $this->document,
            'birth_date' => optional($this->birth_date)->toDateString(),
            'gender' => $this->gender,
            'is_active' => (bool) $this->is_active,
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
