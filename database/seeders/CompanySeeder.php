<?php

namespace Database\Seeders;

use App\Enums\EnvironmentTypeEnum;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class CompanySeeder extends Seeder
{
    public function run()
    {
        if (App::environment(EnvironmentTypeEnum::productEnv())) {
            return;
        }

        Company::factory(10)->create();  // Создает 10 компаний
    }
}
