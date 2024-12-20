<?php

namespace Database\Seeders;

use App\Enums\EnvironmentTypeEnum;
use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class ClientSeeder extends Seeder
{
    public function run()
    {
        if (App::environment(EnvironmentTypeEnum::productEnv())) {
            return;
        }

        Client::factory(10)->create();
    }
}
