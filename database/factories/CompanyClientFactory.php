<?php

namespace Database\Factories;

use App\Models\CompanyClient;
use App\Models\Company;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyClientFactory extends Factory
{
    protected $model = CompanyClient::class;

    public function definition()
    {
        return [
            'id' => $this->faker->uuid,
            'company_id' => Company::factory(),
            'client_id' => Client::factory(),
        ];
    }
}