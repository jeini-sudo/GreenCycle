<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\TreeStatus;
use App\Models\Tree;
use Illuminate\Support\Facades\DB;

class DecayController extends Controller
{
    // controlador que se encarga del punto S2.3 Salud y deterioro
    private const DECAY_DAMAGE = 20;
    private const DECAY_INTERVAL_HOURS = 24;

    public function applyDecay(Tree $tree): void
    {
        DB::transaction(function () use ($tree) {

            $lockedTree = Tree::whereKey($tree->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                $lockedTree->status === TreeStatus::Dead ||
                $lockedTree->status === TreeStatus::Harvested ||
                $lockedTree->status === TreeStatus::Mature
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
