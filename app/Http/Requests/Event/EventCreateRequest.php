<?php

namespace AppHttpRequestsEvent;

use App\Enums\RepeatTypeEnum;
use App\Http\Requests\Traits\EventTrait;
use App\Rules\EnumRule;
use Illuminate\Foundation\Http\FormRequest;

class EventCreateRequest extends FormRequest
{
    use EventTrait;

    public function rules(): array
    {
        return [
            'client_id' => 'required|uuid|exists:clients,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_type' => 'required|string|in:meeting,task',
            'event_time' => 'required|date|after:now',
            'repeat_type' => ['required', new EnumRule(RepeatTypeEnum::class)],
        ];
    }
}
