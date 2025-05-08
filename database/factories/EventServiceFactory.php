<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventService;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventServiceFactory extends Factory
{
    protected $model = EventService::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'service_id' => Service::factory(),
        ];
    }
}