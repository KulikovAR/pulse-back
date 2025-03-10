<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition()
    {
        return [
            'id' => $this->faker->uuid,
            'company_id' => Company::factory(),
            'name' => $this->faker->words(2, true),
        ];
    }
}