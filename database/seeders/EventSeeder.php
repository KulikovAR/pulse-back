<?php

namespace Database\Seeders;

use App\Enums\EnvironmentTypeEnum;
use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class EventSeeder extends Seeder
{
    public function run()
    {
        if (App::environment(EnvironmentTypeEnum::productEnv())) {
            return;
        }

        Event::factory(50)->create();
    }
}
