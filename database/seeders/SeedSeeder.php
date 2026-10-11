<?php

namespace Database\Seeders;

use App\Models\Seed;
use Illuminate\Database\Seeder;

class SeedSeeder extends Seeder
{
    public function run(): void
    {
        Seed::create([
            'seed_name' => 'Semilla básica',
            'special_seed' => false,
            'cares_per_level' => 5,
            'harvest_reward' => 10,
        ]);

        Seed::create([
            'seed_name' => 'Semilla especial',
            'special_seed' => true,
            'cares_per_level' => 3,
            'harvest_reward' => 7,
        ]);
    }
}
