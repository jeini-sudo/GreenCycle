<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TreeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
            'level'=>$this->level,
            'progress'=>$this->progress,
            'health'=>$this->health,
            'status'=>$this->status,
            'seed' => $this->whenLoaded('seed', fn () => [
                'id' => $this->seed->id,
                'seed_name' => $this->seed->seed_name,
                'cares_per_level' => $this->seed->cares_per_level,
                'special_seed'=> $this->seed->special_seed,
            ]),
            'last_care_at'=>$this->last_care_at?->toISOString(),
            'next_care_at' => $this->next_care_at?->toISOString(),
            'last_decay_at'=>$this->last_decay_at?->toISOString(),
            'harvested_at'=>$this->harvested_at?->toISOString(),
            

        ];
    }
}
