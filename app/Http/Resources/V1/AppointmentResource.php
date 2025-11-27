<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'status' => $this->status,
            'scheduled_date' => optional($this->scheduled_date)->toDateString(),
            'scheduled_time' => optional($this->scheduled_time)->format('H:i'),
            'notes' => $this->notes,
            'canceled_at' => optional($this->canceled_at)->toIso8601String(),
            'completed_at' => optional($this->completed_at)->toIso8601String(),
            'doctor' => new DoctorResource($this->whenLoaded('doctor')),
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'schedule' => new DoctorScheduleResource($this->whenLoaded('schedule')),
        ];
    }
}
