<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\TelegramClient;
use Illuminate\Database\Eloquent\Factories\Factory;

class TelegramClientFactory extends Factory
{
    protected $model = TelegramClient::class;

    public function definition()
    {
        return [
            'id' => $this->faker->uuid,
            'client_id' => Client::factory(),
            'chat_id' => $this->faker->unique()->userName,
        ];
    }
}
