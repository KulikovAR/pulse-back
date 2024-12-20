<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Вызов всех сидеров
        $this->call([
            UserSeeder::class,
            ClientSeeder::class,
            TelegramClientSeeder::class,
            CompanySeeder::class,
            EventSeeder::class,
            BitrixIntegrationSeeder::class,
        ]);
    }
}
