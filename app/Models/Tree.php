<?php

namespace App\Models;

use App\Enums\TreeStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tree extends Model
{
    // Al plantar, el cliente SOLO elige la semilla. user_id lo pone la relación
    // ($user->trees()->create(...)); nivel, progreso, salud, estado y fechas
    // los controla el servidor, por eso NO van aquí.
    protected $fillable = [
        'seed_id',
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

    // Los belongsTo van en SINGULAR: Laravel deduce la llave foránea del nombre
    // del método (seed() -> seed_id, user() -> user_id).
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
}
