<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTreeRequest;
use App\Http\Resources\TreeResource;
use App\Models\Tree;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Seed;
use App\Models\ShopItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TreeController extends Controller
{
    public function index(Request $request)
    {
        //
        $tree = $request->user()
            ->trees()
            ->with('seed')
            ->latest()
            ->paginate(15);

        return TreeResource::collection($tree);
    }

    public function store(StoreTreeRequest $request)
    {
        $seed = Seed::findOrFail($request->integer('seed_id'));

        $tree = $seed->special_seed
            ? $this->createSpecialTree($request, $seed)
            : $request->user()->trees()->create($request->validated());

        return (new TreeResource($tree->load('seed')))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Request $request, Tree $tree)
    {
        //
        $this->ensureUserOwnsTree($request, $tree);

        return new TreeResource($tree->load('seed'));
    }

    private function ensureUserOwnsTree(Request $request, Tree $tree): void
    {
        abort_unless($tree->user_id === $request->user()->id, Response::HTTP_NOT_FOUND);
    }


    private function createSpecialTree(StoreTreeRequest $request, Seed $seed): Tree
    {
        return DB::transaction(function () use ($request, $seed) {
            $inventory = $request->user()
                ->inventory()
                ->whereHas('shopItem', function ($query) {
                    $query->where('name', 'Semilla especial');
                })
                ->lockForUpdate()
                ->first();

            if (!$inventory || $inventory->quantity <= 0) {
                throw ValidationException::withMessages([
                    'seed_id' => 'No tienes semillas especiales disponibles.',
                ]);
            }

            $tree = $request->user()->trees()->create([
                'seed_id' => $seed->id,
            ]);

            $inventory->decrement('quantity');

            return $tree;
        });
    }
}
