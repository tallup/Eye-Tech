<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRequestResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'request_number' => $this->request_number,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'customer_email' => $this->customer_email,
            'service_id' => $this->service_id,
            'service' => $this->whenLoaded('service', fn () => [
                'id' => $this->service->id,
                'name' => $this->service->name,
                'price' => (float) $this->service->price,
            ]),
            'device_description' => $this->device_description,
            'problem_description' => $this->problem_description,
            'status' => $this->status,
            'estimated_cost' => $this->estimated_cost !== null ? (float) $this->estimated_cost : null,
            'final_cost' => $this->final_cost !== null ? (float) $this->final_cost : null,
            'notes' => $this->notes,
            'completed_at' => $this->completed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
