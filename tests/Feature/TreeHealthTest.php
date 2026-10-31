<?php

namespace Tests\Feature;

use App\Enums\TreeStatus;
use App\Http\Controllers\Api\V1\TreeHealthController;
use App\Models\Seed;
use App\Models\Tree;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


//pruebas deterioro y demás
class TreeHealthTest extends TestCase
{
    use RefreshDatabase;

    // Create a tree with the required user and seed.
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
        // The tree has gone 23 hours without care.
        $tree = $this->createTree();

        $tree->forceFill([
            'last_care_at' => now()->subHours(23),
        ])->save();

        (new TreeHealthController())->applyDecay($tree);

        // No complete day has passed, so health stays at 100.
        $this->assertEquals(100, $tree->fresh()->health);
    }

    public function test_tree_loses_20_health_after_24_hours(): void
    {
        $tree = $this->createTree();

        $tree->forceFill([
            'last_care_at' => now()->subHours(24),
        ])->save();

        (new TreeHealthController())->applyDecay($tree);

        // One complete day causes 20 damage.
        $this->assertEquals(80, $tree->fresh()->health);
    }

    public function test_tree_loses_40_health_after_48_hours(): void
    {
        $tree = $this->createTree();

        $tree->forceFill([
            'last_care_at' => now()->subHours(48),
        ])->save();

        (new TreeHealthController())->applyDecay($tree);

        // Two complete days cause 40 damage.
        $this->assertEquals(60, $tree->fresh()->health);
    }

    public function test_decay_is_not_applied_twice(): void
    {
        $tree = $this->createTree();

        $tree->forceFill([
            'last_care_at' => now()->subHours(48),
        ])->save();

        $controller = new TreeHealthController();

        // First execution applies two days of damage.
        $controller->applyDecay($tree);
        $this->assertEquals(60, $tree->fresh()->health);

        // Second execution must not repeat that damage.
        $controller->applyDecay($tree);
        $this->assertEquals(60, $tree->fresh()->health);
    }

    public function test_tree_dies_when_health_reaches_zero(): void
    {
        $tree = $this->createTree();

        $tree->forceFill([
            'last_care_at' => now()->subHours(120),
        ])->save();

        (new TreeHealthController())->applyDecay($tree);

        // Five days cause 100 damage and kill the tree.
        $this->assertEquals(0, $tree->fresh()->health);
        $this->assertEquals(TreeStatus::Dead, $tree->fresh()->status);
    }

    public function test_care_restores_health_without_exceeding_100(): void
    {
        $tree = $this->createTree();

        $tree->forceFill([
            'health' => 90,
        ])->save();

        (new TreeHealthController())->restoreHealth($tree);

        // Restoring 20 cannot increase health beyond 100.
        $this->assertEquals(100, $tree->health);
    }

    public function test_dead_tree_cannot_restore_health(): void
    {
        $tree = $this->createTree();

        $tree->forceFill([
            'health' => 0,
            'status' => TreeStatus::Dead,
        ])->save();

        (new TreeHealthController())->restoreHealth($tree);

        // Dead trees cannot recover health.
        $this->assertEquals(0, $tree->health);
    }
}
