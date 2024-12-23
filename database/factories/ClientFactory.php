<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition()
    {
        return [
            'id' => $this->faker->uuid,
            'user_id' => null,
            'name' => $this->faker->name,
            'phone' => $this->faker->phoneNumber,
        ];
    }
}
