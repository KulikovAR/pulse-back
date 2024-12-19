<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class EventCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'client_id' => 'required|uuid|exists:clients,id',
            'company_id' => 'required|uuid|exists:companies,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'required|string|in:meeting,task',
            'event_time' => 'required|date|after:now',
            'repeat_type' => 'nullable|string|in:daily,weekly,monthly',
        ];
    }
}
