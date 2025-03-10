<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|uuid|exists:users,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|string|url',
            'category' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ];
    }
}