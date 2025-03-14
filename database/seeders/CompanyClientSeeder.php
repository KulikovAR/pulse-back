<?php

namespace Database\Seeders;

use App\Enums\EnvironmentTypeEnum;
use App\Models\CompanyClient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class CompanyClientSeeder extends Seeder
{
    public function run()
    {
        if (App::environment(EnvironmentTypeEnum::productEnv())) {
            return;
        }

        CompanyClient::factory(10)->create();
    }
}