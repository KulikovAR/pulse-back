<?php

namespace Database\Seeders;

use App\Enums\EnvironmentTypeEnum;
use App\Models\TelegramClient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class TelegramClientSeeder extends Seeder
{
    public function run()
    {
        if (App::environment(EnvironmentTypeEnum::productEnv())) {
            return;
        }

        TelegramClient::factory(10)->create();
    }
}
