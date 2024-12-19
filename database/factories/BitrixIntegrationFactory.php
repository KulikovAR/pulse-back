<?php

namespace Database\Factories;

use App\Models\BitrixIntegration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BitrixIntegrationFactory extends Factory
{
    protected $model = BitrixIntegration::class;

    public function definition()
    {
        return [
            'id' => $this->faker->uuid,
            'user_id' => User::factory(),
            'token' => $this->faker->uuid,
        ];
    }
}
