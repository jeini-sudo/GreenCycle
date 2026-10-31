<?php

namespace Tests\Feature;

use App\Enums\TreeStatus;
use App\Models\Seed;
use App\Models\Tree;
use App\Models\User;
use Database\Seeders\SeedSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TreeCareTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Tree $tree;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SeedSeeder::class);

        $this->user = User::factory()->create();
        $this->tree = $this->user->trees()->create([
            'seed_id' => Seed::where('seed_name', 'Semilla básica')->value('id'),
        ]);

        Sanctum::actingAs($this->user);
    }

    public function test_user_can_care_for_own_active_tree(): void
    {
        $this->tree->forceFill(['health' => 50])->save();

        $this->postJson("/api/trees/{$this->tree->id}/care")
            ->assertOk()
            ->assertJsonPath('message', 'Cuidado aplicado con éxito')
            ->assertJsonPath('tree.id', $this->tree->id)
            ->assertJsonPath('tree.progress', 1)
            ->assertJsonPath('tree.health', 70)
            ->assertJsonPath('tree.status', 'ACTIVE');

        $tree = $this->tree->fresh();

        $this->assertSame(1, $tree->progress);
        $this->assertNotNull($tree->next_care_at);
        $this->assertTrue($tree->next_care_at->isFuture());
    }

    public function test_second_care_during_cooldown_returns_409_without_changing_progress(): void
    {
        $this->postJson("/api/trees/{$this->tree->id}/care")->assertOk();

        $this->postJson("/api/trees/{$this->tree->id}/care")
            ->assertStatus(409)
            ->assertJsonStructure(['error', 'next_care_at']);

        $this->assertSame(1, $this->tree->fresh()->progress);
    }

    public function test_five_cares_raise_the_level_and_reset_progress(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson("/api/trees/{$this->tree->id}/care")->assertOk();

            // Skips the 60-minute cooldown so the next care is allowed.
            $this->travel(61)->minutes();
        }

        $tree = $this->tree->fresh();

        $this->assertSame(1, $tree->level);
        $this->assertSame(0, $tree->progress);
    }

    public function test_tree_becomes_mature_when_it_reaches_level_ten(): void
    {
        $this->tree->forceFill(['level' => 9, 'progress' => 4])->save();

        $this->postJson("/api/trees/{$this->tree->id}/care")
            ->assertOk()
            ->assertJsonPath('tree.level', 10)
            ->assertJsonPath('tree.progress', 0)
            ->assertJsonPath('tree.status', 'MATURE');

        $this->assertSame(TreeStatus::mature, $this->tree->fresh()->status);
    }

    public function test_user_cannot_care_for_another_users_tree(): void
    {
        $owner = User::factory()->create();
        $foreignTree = $owner->trees()->create(['seed_id' => $this->tree->seed_id]);

        $this->postJson("/api/trees/{$foreignTree->id}/care")->assertForbidden();

        $this->assertSame(0, $foreignTree->fresh()->progress);
    }

    public function test_mature_tree_cannot_receive_more_care(): void
    {
        $this->tree->forceFill(['status' => TreeStatus::mature])->save();

        $this->postJson("/api/trees/{$this->tree->id}/care")->assertStatus(409);

        $this->assertSame(0, $this->tree->fresh()->progress);
    }
}