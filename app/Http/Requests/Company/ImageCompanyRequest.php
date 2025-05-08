<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class ImageCompanyRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'image' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
