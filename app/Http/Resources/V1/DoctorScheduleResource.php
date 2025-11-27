<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="DoctorSchedule",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="weekday", type="integer", example=1),
 *     @OA\Property(property="start_time", type="string", example="09:00"),
 *     @OA\Property(property="end_time", type="string", example="12:00"),
 *     @OA\Property(property="slot_duration", type="integer", example=30),
 *     @OA\Property(property="is_active", type="boolean")
 * )
 */
class DoctorScheduleResource extends JsonResource
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
            'weekday' => $this->weekday,
            'start_time' => optional($this->start_time)->format('H:i'),
            'end_time' => optional($this->end_time)->format('H:i'),
            'slot_duration' => $this->slot_duration,
            'is_active' => (bool) $this->is_active,
        ];
    }
}
