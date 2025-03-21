<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventRepeat;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventRepeatFactory extends Factory
{
    protected $model = EventRepeat::class;

    public function definition()
    {
        return [
            'event_id' => Event::factory(),
            'event_time' => $this->faker->dateTimeBetween('now', '+1 year'),
        ];
    }
}