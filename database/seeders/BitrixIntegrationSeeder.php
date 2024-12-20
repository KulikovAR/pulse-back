<?php

namespace Database\Seeders;

use App\Enums\EnvironmentTypeEnum;
use App\Models\BitrixIntegration;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class BitrixIntegrationSeeder extends Seeder
{
    public function run()
    {
        if (App::environment(EnvironmentTypeEnum::productEnv())) {
            return;
        }

        BitrixIntegration::factory(10)->create();
    }
}
