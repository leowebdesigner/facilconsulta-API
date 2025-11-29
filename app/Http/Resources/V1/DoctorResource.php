<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Doctor",
 *     type="object",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="crm", type="string"),
 *     @OA\Property(property="specialty", type="string"),
 *     @OA\Property(property="address", type="string"),
 *     @OA\Property(property="experience_years", type="integer"),
 *     @OA\Property(property="bio", type="string"),
 *     @OA\Property(property="is_active", type="boolean"),
 *     @OA\Property(property="schedules", type="array", @OA\Items(ref="#/components/schemas/DoctorSchedule")),
 *     @OA\Property(
 *         property="availability",
 *         type="array",
 *         nullable=true,
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="date", type="string", format="date"),
 *             @OA\Property(property="weekday", type="integer"),
 *             @OA\Property(property="slots", type="array", @OA\Items(type="string", example="13:00"))
 *         )
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time")
 * )
 */
class DoctorResource extends JsonResource
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
            'crm' => $this->crm,
            'specialty' => $this->specialty,
            'address' => $this->address,
            'experience_years' => $this->experience_years,
            'bio' => $this->bio,
            'is_active' => (bool) $this->is_active,
            'schedules' => DoctorScheduleResource::collection(
                $this->whenLoaded('schedules')
            ),
            'availability' => $this->when(isset($this->availability), $this->availability),
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
