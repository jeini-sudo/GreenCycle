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
        
        $this->ensureUserOwnsTree($request, $tree);     


       if ($tree->status !== TreeStatus::Active) {
    return response()->json([
        'error' => 'Este árbol ya no puede recibir más cuidados.',
    ], Response::HTTP_CONFLICT);

    
}

        if ($tree->next_care_at?->isFuture()) {
            $minutesLeft = (int) ceil(now()->diffInMinutes($tree->next_care_at));

            throw ValidationException::withMessages([
                'care' => "This tree is on cooldown. Try again in {$minutesLeft} minute(s).",
            ]);
        }

        $this->applyCareToTree($tree);


        return response()->json([
    'message' => 'Cuidado aplicado con éxito',
    'tree' => new TreeResource($tree),
]);
    }

    private function applyCareToTree(Tree $tree): void
    {
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
            'status' => $newLevel >= self::MAX_LEVEL ? TreeStatus::Mature : TreeStatus::Active,
            'last_care_at' => now(),
            'next_care_at' => now()->addMinutes(self::COOLDOWN_MINUTES),
        ])->save();
    }


    private function ensureUserOwnsTree(Request $request, Tree $tree): void
{
    abort_unless($tree->user_id === $request->user()->id, Response::HTTP_FORBIDDEN);
}
}