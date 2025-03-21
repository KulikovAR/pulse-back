<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EnumRule implements Rule
{
    private string $enumClass;

    public function __construct(string $enumClass)
    {
        $this->enumClass = $enumClass;
    }

    public function passes($attribute, $value): bool
    {
        return in_array($value, array_column($this->enumClass::cases(), 'value'));
    }

    public function message(): string
    {
        return 'The :attribute must be a valid value for '.$this->enumClass;
    }
}
