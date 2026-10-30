<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\TreeStatus;
use App\Models\Tree;
use Illuminate\Support\Facades\DB;

class TreeHealthController extends Controller
{
    // controlador que se encarga del punto S2.3 Salud y deterioro
    private const MAX_HEALTH = 100;
    private const HEALTH_RECOVERY = 20;
    private const DECAY_DAMAGE = 20;
    private const DECAY_INTERVAL_HOURS = 24;


    public function restoreHealth(Tree $tree): void
    {
        // no recuperar salud si esta muerto o cosechado o maduro
        if ($tree->status === TreeStatus::Dead || $tree->status === TreeStatus::Harvested || $tree->status === TreeStatus::Mature) {
            return;
        }

        $newHealth = min(
            self::MAX_HEALTH,
            $tree->health + self::HEALTH_RECOVERY
        );

        $tree->health = $newHealth;
    }

    public function applyDecay(Tree $tree): void
    {
        DB::transaction(function () use ($tree) {

            $lockedTree = Tree::whereKey($tree->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $lockedTree->status === TreeStatus::Dead ||
                $lockedTree->status === TreeStatus::Harvested
            ) {
                return;
            }

            $startDate = $lockedTree->last_care_at
                ?? $lockedTree->created_at;

            if ($startDate === null) {
                return;
            }

            if (
                $lockedTree->last_decay_at !== null &&
                $lockedTree->last_decay_at->greaterThan($startDate)
            ) {
                $startDate = $lockedTree->last_decay_at;
            }

            $elapsedSeconds = max(
                0,
                now()->getTimestamp() - $startDate->getTimestamp()
            );

            $intervalSeconds = self::DECAY_INTERVAL_HOURS * 3600;

            $completedIntervals = intdiv(
                $elapsedSeconds,
                $intervalSeconds
            );

            if ($completedIntervals === 0) {
                return;
            }

            $newHealth = max(
                0,
                $lockedTree->health -
                ($completedIntervals * self::DECAY_DAMAGE)
            );


            $lastDecayAt = $startDate->copy()->addSeconds(
                $completedIntervals * $intervalSeconds
            );

            $lockedTree->forceFill([
                'health' => $newHealth,
                'last_decay_at' => $lastDecayAt,
                'status' => $newHealth === 0
                    ? TreeStatus::Dead
                    : $lockedTree->status,
            ])->save();

        }, 3);

        $tree->refresh();
    }
}
