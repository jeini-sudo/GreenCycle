<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EffectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'shop_item_id' => $this->shop_item_id,
            'tree_id' => $this->tree_id,
            'type' => $this->type,
            'name' => $this->shopItem->name,
            'started_time' => $this->started_time,
            'expires_at' => $this->expires_at,
        ];
    }
}
