<?php

namespace App\Http\Requests\Traits;

use App\Dto\EventDto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\UnauthorizedException;

trait EventTrait
{
    public function toEventDto(): EventDto
    {
        $user = Auth::user();
        $company = $user->company;

        if ($company->user_id !== $user->id) {
            throw new UnauthorizedException('You are not authorized to create events for this company.');
        }

        return new EventDto(
            id: $this->input('id'),
            clientId: $this->input('client_id'),
            companyId: $company->id,
            serviceId: $this->input('service_id'),
            description: $this->input('description'),
            eventType: $this->input('event_type'),
            eventTime: $this->input('event_time'),
            repeatType: $this->input('repeat_type'),
            targetTime: $this->input('target_time'),
        );
    }
}
