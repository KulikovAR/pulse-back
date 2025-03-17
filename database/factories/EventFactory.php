<?php

namespace Database\Factories;

use App\Enums\RepeatTypeEnum;
use App\Models\Client;
use App\Models\Company;
use App\Models\Event;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        return [
            'id' => $this->faker->uuid,
            'client_id' => Client::factory(),
            'company_id' => Company::factory(),
            'service_id' => Service::factory(),
            'description' => $this->faker->sentence,
            'event_type' => $this->faker->randomElement(['meeting', 'call', 'task']),
            'event_time' => $this->faker->dateTimeBetween('now', '+1 week'),
            'repeat_type' => $this->faker->randomElement(RepeatTypeEnum::get()),
        ];
    }
}
