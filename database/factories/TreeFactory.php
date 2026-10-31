<?php

namespace Database\Factories;

use App\Models\Tree;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tree>
 */
class TreeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // $table->id();
            'user_id' => User::factory(),
            'seed_id' => Seed::factory(),
            'level' => 0,
            'progress' => 0,
            'health' => 100,
            'status' => TreeStatus::Active,
        ];
    }
}
