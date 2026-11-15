<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
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
            'name' => $this->shopItem->name,
            'description' => $this->shopItem->description,
            'quantity' => $this->quantity,
        ];
    }
}
