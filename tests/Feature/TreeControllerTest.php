<?php

namespace Tests\Feature;

use App\Models\Seed;
use App\Models\User;
use Database\Seeders\SeedSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TreeControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $testUser;
    private Seed $testSeed;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SeedSeeder::class);

        $this->testUser = User::factory()->create();
        $this->testSeed = Seed::query()->firstOrFail();

        Sanctum::actingAs($this->testUser);
    }

    public function test_user_can_create_tree_with_initial_values(): void
    {
        $this->postJson('/api/trees', [
            'seed_id' => $this->testSeed->id,
        ])
            ->assertCreated()
            ->assertJsonPath('data.level', 0)
            ->assertJsonPath('data.progress', 0)
            ->assertJsonPath('data.health', 100)
            ->assertJsonPath('data.status', 'ACTIVE');

        $this->assertDatabaseHas('trees', [
            'user_id' => $this->testUser->id,
            'seed_id' => $this->testSeed->id,
            'level' => 0,
            'progress' => 0,
            'health' => 100,
            'status' => 'ACTIVE',
        ]);
    }

    public function test_user_cannot_create_tree_with_invalid_seed(): void
    {
        $this->postJson('/api/trees', [
            'seed_id' => 999999,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['seed_id']);

        $this->assertDatabaseCount('trees', 0);
    }

    public function test_user_cannot_view_another_users_tree(): void
    {
        $owner = User::factory()->create();

        $tree = $owner->trees()->create([
            'seed_id' => $this->testSeed->id,
        ]);

        $this->getJson("/api/trees/{$tree->id}")
            ->assertNotFound();
    }

    public function test_unauthenticated_user_cannot_access_trees(): void
    {
        $this->app['auth']->forgetGuards();

        $this->getJson('/api/trees')
            ->assertUnauthorized();

        $this->postJson('/api/trees', [
            'seed_id' => $this->testSeed->id,
        ])
            ->assertUnauthorized();
    }

    public function test_user_can_list_only_own_trees(): void
    {
        $this->testUser->trees()->create([
            'seed_id' => $this->testSeed->id,
        ]);

        $otherUser = User::factory()->create();

        $otherUser->trees()->create([
            'seed_id' => $this->testSeed->id,
        ]);

        $this->getJson('/api/trees')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }
}
