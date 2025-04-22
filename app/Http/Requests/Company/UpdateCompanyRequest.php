<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'user_id'     => 'sometimes|required|uuid|exists:users,id',
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'image'       => 'nullable',
            'category'    => 'sometimes|required|string|max:255',
            'address'     => 'sometimes|required|string|max:255',
        ];
    }
}
