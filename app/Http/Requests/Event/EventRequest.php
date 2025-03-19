<?php

namespace App\Http\Requests\Event;

use App\Enums\RepeatTypeEnum;
use App\Http\Requests\Traits\EventTrait;
use App\Rules\EnumRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EventRequest extends FormRequest
{
    use EventTrait;

    public function rules(): array
    {
        return [
            'id' => 'nullable|uuid',
            'client_id' => 'required|uuid|exists:clients,id',
            'service_ids' => 'required|array',
            'service_ids.*' => 'required|uuid|exists:services,id',
            'description' => 'nullable|string',
            'event_type' => 'nullable|string|in:meeting,task',
            'event_time' => 'required|date|after:now',
            'repeat_type' => ['nullable', new EnumRule(RepeatTypeEnum::class)],
            'target_time' => 'nullable|date|after:now',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'company_id' => Auth::user()->company->id
        ]);
    }
}
