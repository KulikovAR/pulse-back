<?php

namespace App\Http\Requests\Telegram;

use Illuminate\Foundation\Http\FormRequest;

class TelegramClientLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'nullable|numeric',
            'first_name' => 'nullable|string',
            'username' => 'nullable|string',
            'phone' => 'nullable|string',
        ];
    }
}
