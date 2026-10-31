<?php

namespace Database\Factories;

use App\Models\Seed;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Seed>
 */
class SeedFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'user_id' => User::factory(),
            'seed_name' => fake()->unique()->word() . 'Seed',
            'special_seed' => false,
            'cares_per_level' => 5,
            'harvest_reward' => 10,
        ];
    }
}
