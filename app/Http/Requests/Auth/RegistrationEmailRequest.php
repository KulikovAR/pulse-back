<?php

namespace App\Http\Requests\Auth;

use App\Traits\EmailPasswordRules;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationEmailRequest extends FormRequest
{
    use EmailPasswordRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => $this->emailCreationRules(),
            'password' => $this->passwordCreationRules(),
        ];
    }
}
