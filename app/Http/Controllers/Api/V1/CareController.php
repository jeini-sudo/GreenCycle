<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\TreeStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\TreeResource;
use App\Models\Tree;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Effect;

class CareController extends Controller
{
    private const COOLDOWN_MINUTES = 60;

    private const ACCELERATOR_COOLDOWN_MINUTES = 30;

    private const HEALTH_PER_CARE = 20;

    private const MAX_LEVEL = 10;

    public function store(Request $request, Tree $tree)
    {
        $result = DB::transaction(function () use ($request, $tree) {
            $tree = Tree::query()
                ->whereKey($tree->id)
                ->lockForUpdate()
                ->firstOrFail();

            $this->ensureUserOwnsTree($request, $tree);

            if ($tree->status !== TreeStatus::Active) {
                return response()->json([
                    'code' => 'TREE_CARE_NOT_ALLOWED',
                    'error' => 'Este árbol ya no puede recibir más cuidados.',
                ], Response::HTTP_CONFLICT);
            }

            if ($tree->next_care_at?->isFuture()) {
                return response()->json([
                    'code' => 'TREE_CARE_COOLDOWN',
                    'error' => 'El árbol todavía está en tiempo de espera.',
                    'next_care_at' => $tree->next_care_at->toISOString(),
                ], Response::HTTP_CONFLICT);
            }

            $acceleratorActive = $tree->effects()
                ->where('type', 'accelerator')
                ->where('status', true)
                ->where('expires_at', '>', now())
                ->exists();

            $this->applyCareToTree(
                $tree,
                $acceleratorActive
                ? self::ACCELERATOR_COOLDOWN_MINUTES
                : self::COOLDOWN_MINUTES
            );

            return response()->json([
                'message' => 'Cuidado aplicado con éxito',
                'tree' => new TreeResource($tree->load('seed')),
            ], Response::HTTP_OK);
        }, 3);

        return $result;
    }

    private function applyCareToTree(
        Tree $tree,
        int $cooldownMinutes = self::COOLDOWN_MINUTES
    ): void {
        $newProgress = $tree->progress + 1;
        $newLevel = $tree->level;

        if ($newProgress >= $tree->seed->cares_per_level) {
            $newLevel++;
            $newProgress = 0;
        }

        $tree->forceFill([
            'progress' => $newProgress,
            'level' => $newLevel,
            'health' => min(100, $tree->health + self::HEALTH_PER_CARE),
            'status' => $newLevel >= self::MAX_LEVEL
                ? TreeStatus::Mature
                : TreeStatus::Active,
            'last_care_at' => now(),
            'next_care_at' => now()->addMinutes($cooldownMinutes),
        ])->save();
    }

    public function processAutomaticWatering(): void
    {
        $effectIds = Effect::query()
            ->where('type', 'automatic_watering')
            ->where('status', true)
            ->where('expires_at', '>', now())
            ->pluck('id');

        foreach ($effectIds as $effectId) {
            DB::transaction(function () use ($effectId) {
                $candidate = Effect::find($effectId);

                if (!$candidate) {
                    return;
                }

                $tree = Tree::query()
                    ->whereKey($candidate->tree_id)
                    ->lockForUpdate()
                    ->first();

                if (!$tree) {
                    return;
                }

                $effect = Effect::query()
                    ->whereKey($effectId)
                    ->lockForUpdate()
                    ->first();

                if (
                    !$effect ||
                    !$effect->status ||
                    !$effect->expires_at?->isFuture()
                ) {
                    return;
                }

                if ($tree->status !== TreeStatus::Active) {
                    $effect->update(['status' => false]);

                    return;
                }

                if ($tree->next_care_at?->isFuture()) {
                    return;
                }

                $this->applyCareToTree($tree);
            }, 3);
        }
    }

    private function ensureUserOwnsTree(Request $request, Tree $tree): void
    {
        abort_unless($tree->user_id === $request->user()->id, Response::HTTP_FORBIDDEN);
    }
}
