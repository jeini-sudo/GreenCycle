<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\EffectResource;
use App\Http\Resources\InventoryResource;
use App\Models\Effect;
use Illuminate\Http\Request;
use App\Enums\TreeStatus;
use App\Http\Requests\Api\V1\UseItemRequest;
use App\Models\Tree;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $inventory = $request->user()
            ->inventory()
            ->with('shopItem')
            ->where('quantity', '>', 0)
            ->latest()
            ->get();

        $activeEffects = Effect::query()
            ->with('shopItem')
            ->whereHas('tree', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->where('status', true)
            ->where('expires_at', '>', now())
            ->get();

        return [
            'inventory' => InventoryResource::collection($inventory),
            'active_effects' => EffectResource::collection($activeEffects),
        ];
    }

    public function useItem(UseItemRequest $request, int $item)
    {
        return DB::transaction(function () use ($request, $item) {
            $tree = Tree::query()
                ->whereKey($request->validated('tree_id'))
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $tree->user_id === $request->user()->id,
                Response::HTTP_FORBIDDEN
            );

            if ($tree->status !== TreeStatus::Active) {
                return response()->json([
                    'code' => 'TREE_ITEM_NOT_ALLOWED',
                    'error' => 'Este árbol no puede recibir mejoras.',
                ], Response::HTTP_CONFLICT);
            }

            $inventory = $request->user()
                ->inventory()
                ->with('shopItem')
                ->where('shop_item_id', $item)
                ->lockForUpdate()
                ->first();

            if (!$inventory || $inventory->quantity <= 0) {
                return response()->json([
                    'code' => 'ITEM_NOT_AVAILABLE',
                    'error' => 'El ítem no está disponible.',
                ], Response::HTTP_CONFLICT);
            }

            $effectType = match ($inventory->shopItem->name) {
                'Acelerador' => 'accelerator',
                'Riego automático' => 'automatic_watering',
                default => null,
            };

            if ($effectType === null) {
                return response()->json([
                    'code' => 'ITEM_NOT_SUPPORTED',
                    'error' => 'Este ítem no tiene una regla de uso configurada.',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $alreadyActive = $tree->effects()
                ->where('type', $effectType)
                ->where('status', true)
                ->where('expires_at', '>', now())
                ->exists();

            if ($alreadyActive) {
                return response()->json([
                    'code' => 'EFFECT_ALREADY_ACTIVE',
                    'error' => 'Este efecto ya está activo.',
                ], Response::HTTP_CONFLICT);
            }

            $duration = match ($effectType) {
                'accelerator' => 120,
                'automatic_watering' => 360,
            };

            $effect = $tree->effects()->create([
                'shop_item_id' => $inventory->shop_item_id,
                'type' => $effectType,
                'started_time' => now(),
                'expires_at' => now()->addMinutes($duration),
                'status' => true,
            ]);

            $inventory->decrement('quantity');

            return [
                'message' => 'Ítem utilizado correctamente.',
                'effect' => new EffectResource($effect->load('shopItem')),
                'remaining_quantity' => $inventory->fresh()->quantity,
            ];
        }, 3);
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        //
    }
}
