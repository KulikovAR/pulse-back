<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition()
    {
        return [
            'id' => $this->faker->uuid,
            'user_id' => User::factory(),
            'name' => $this->faker->company,
            'description' => $this->faker->sentence,
            'image' => $this->faker->imageUrl,
            'category' => $this->faker->word,
            'address' => $this->faker->address,
        ];
    }
}
