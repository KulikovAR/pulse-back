<?php

namespace Database\Seeders;

use App\Enums\EnvironmentTypeEnum;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        if (App::environment(EnvironmentTypeEnum::productEnv())) {
            return;
        }

        Service::factory(10)->create();
    }
}