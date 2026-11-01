<?php

namespace Tests\Feature;

use App\Enums\TreeStatus;
use App\Http\Controllers\Api\V1\DecayController;
use App\Models\Seed;
use App\Models\Tree;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


//pruebas deterioro y demás
class DecayTest extends TestCase
{
    use RefreshDatabase;


    private function createTree(): Tree
    {
        $user = User::factory()->create();

        $seed = Seed::create([
            'seed_name' => 'Basic Seed',
            'special_seed' => false,
            'cares_per_level' => 5,
            'harvest_reward' => 10,
        ]);

        return $user->trees()->create([
            'seed_id' => $seed->id,
        ]);
    }

    public function test_tree_does_not_decay_before_24_hours(): void
    {

        $tree = $this->createTree();

        $tree->forceFill([
            'last_care_at' => now()->subHours(23),
        ])->save();

        (new DecayController())->applyDecay($tree);


        $this->assertEquals(100, $tree->fresh()->health);
    }

    public function test_tree_loses_20_health_after_24_hours(): void
    {
        $tree = $this->createTree();

        $tree->forceFill([
            'last_care_at' => now()->subHours(24),
        ])->save();

        (new DecayController())->applyDecay($tree);

        $this->assertEquals(80, $tree->fresh()->health);
    }

    public function test_tree_loses_40_health_after_48_hours(): void
    {
        $tree = $this->createTree();

        $tree->forceFill([
            'last_care_at' => now()->subHours(48),
        ])->save();

        (new DecayController())->applyDecay($tree);


        $this->assertEquals(60, $tree->fresh()->health);
    }

    public function test_decay_is_not_applied_twice(): void
    {
        $tree = $this->createTree();

        $tree->forceFill([
            'last_care_at' => now()->subHours(48),
        ])->save();

        $controller = new DecayController();

        $controller->applyDecay($tree);
        $this->assertEquals(60, $tree->fresh()->health);


        $controller->applyDecay($tree);
        $this->assertEquals(60, $tree->fresh()->health);
    }

    public function test_tree_dies_when_health_reaches_zero(): void
    {
        $tree = $this->createTree();

        $tree->forceFill([
            'last_care_at' => now()->subHours(120),
        ])->save();

        (new DecayController())->applyDecay($tree);


        $this->assertEquals(0, $tree->fresh()->health);
        $this->assertEquals(TreeStatus::Dead, $tree->fresh()->status);
    }

    public function test_mature_tree_does_not_decay(): void
    {
        $tree = $this->createTree();

        $tree->forceFill([
            'level' => 10,
            'status' => TreeStatus::Mature,
            'last_care_at' => now()->subDays(5),
        ])->save();

        (new DecayController())->applyDecay($tree);

        $this->assertEquals(100, $tree->fresh()->health);
        $this->assertEquals(TreeStatus::Mature, $tree->fresh()->status);
    }

    public function test_harvested_tree_does_not_decay(): void
    {
        $tree = $this->createTree();

        $tree->forceFill([
            'status' => TreeStatus::Harvested,
            'last_care_at' => now()->subDays(5),
        ])->save();

        (new DecayController())->applyDecay($tree);

        $this->assertEquals(100, $tree->fresh()->health);
        $this->assertEquals(TreeStatus::Harvested, $tree->fresh()->status);
    }
}
