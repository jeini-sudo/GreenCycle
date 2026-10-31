<?php

namespace App\Models;


use App\Enums\TreeStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;



class Tree extends Model
{
      protected $fillable = [
        'seed_id',
    ];

     protected $attributes = [
        'level' => 0,
        'progress' => 0,
        'health' => 100,
        'status' => 'active',
    ];

    protected function casts(): array
    {
        return [
            'status' => TreeStatus::class,
            'last_care_at' => 'datetime',
            'next_care_at' => 'datetime',
            'last_decay_at' => 'datetime',
            'harvested_at' => 'datetime',
        ];
    }

   
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

  
    public function seed(): BelongsTo
    {
        return $this->belongsTo(Seed::class);
    }


    public function effects(): HasMany
    {
        return $this->hasMany(Effect::class);
    }

  
    public function harvest(): void
    {
        if ($this->status !== TreeStatus::mature) {
            throw ValidationException::withMessages([
                'tree' => 'Este árbol no está listo para cosechar',
            ]);
        }

      
        DB::transaction(function () {
            $this->forceFill([
                'status' => TreeStatus::harvested,
                'harvested_at' => now(),
            ])->save();

            $this->user->increment('coins', $this->seed->harvest_reward);
        });
    }
}
