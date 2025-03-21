<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\ServiceResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getId(),
            'company_client' => $this->companyClient,
            'company' => $this->company,
            'client_id' => $this->getClientId(),
            'company_id' => $this->getCompanyId(),
            'service_ids' => $this->getServiceIds(),
            'services' => ServiceResource::collection($this->services),
            'description' => $this->getDescription(),
            'event_type' => $this->getEventType(),
            'event_time' => $this->getEventTime(),
            'repeat_type' => $this->getRepeatType(),
            'target_time' => $this->getTargetTime(),
        ];
    }
}
