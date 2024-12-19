<?php

namespace App\Http\Requests\Event;

use App\Dto\EventDto;
use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'id' => 'nullable|uuid',
            'client_id' => 'required|uuid|exists:clients,id',
            'company_id' => 'required|uuid|exists:companies,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'required|string|in:meeting,task',
            'event_time' => 'required|date|after:now',
            'repeat_type' => 'nullable|string|in:daily,weekly,monthly',
        ];
    }

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
