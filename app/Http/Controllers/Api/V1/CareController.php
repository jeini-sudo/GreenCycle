<?php

namespace App\Http\Controllers\Api\V1;


use App\Enums\TreeStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\TreeResource;
use App\Models\Tree;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class CareController extends Controller
{
    private const COOLDOWN_MINUTES = 60;

  
    private const HEALTH_PER_CARE = 20;

       private const MAX_LEVEL = 10;

   
    public function store(Request $request, Tree $tree)
    {
        abort_unless($tree->user_id === $request->user()->id, Response::HTTP_NOT_FOUND);

        // Only growing trees can be cared for (not dead, mature or harvested ones).
        if ($tree->status !== TreeStatus::Active) {
            throw ValidationException::withMessages([
                'care' => 'This tree can no longer be cared for.',
            ]);
        }

        if ($tree->next_care_at?->isFuture()) {
            $minutesLeft = (int) ceil(now()->diffInMinutes($tree->next_care_at));

            throw ValidationException::withMessages([
                'care' => "This tree is on cooldown. Try again in {$minutesLeft} minute(s).",
            ]);
        }

        $this->applyCareToTree($tree);

        return new TreeResource($tree);
    }

    private function applyCareToTree(Tree $tree): void
    {
        $newProgress = $tree->progress + 1;
        $newLevel = min(self::MAX_LEVEL, intdiv($newProgress, $tree->seed->cares_per_level));

        $tree->forceFill([
            'progress' => $newProgress,
            'tree_level' => $newLevel,
            'health' => min(100, $tree->health + self::HEALTH_PER_CARE),
            'status' => $newLevel >= self::MAX_LEVEL ? TreeStatus::Mature : TreeStatus::Active,
            'last_care_at' => now(),
            'next_care_at' => now()->addMinutes(self::COOLDOWN_MINUTES),
        ])->save();
    }
}