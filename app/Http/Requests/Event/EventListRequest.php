<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class EventListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'client_id' => 'required|uuid|exists:clients,id',
        ];
    }
}
