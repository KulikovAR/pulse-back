<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition()
    {
        return [
            'id' => $this->faker->uuid,
            'user_id' => User::factory(),
            'name' => $this->faker->name,
            'phone' => $this->faker->numerify('+7#########'),
        ];
    }
}
