<?php

namespace App\Http\Requests\Traits;

use App\Dto\EventDto;

trait EventTrait
{
    public function toEventDto(): EventDto
    {
        return new EventDto(
            id: $this->input('id'),
            clientId: $this->input('client_id'),
            companyId: $this->input('company_id'),
            name: $this->input('name'),
            description: $this->input('description'),
            eventType: $this->input('event_type'),
            eventTime: $this->input('event_time'),
            repeatType: $this->input('repeat_type'),
        );
    }
}
