<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seed extends Model
{
    // Es catálogo del juego (lo siembra el seeder), no lo escribe el cliente.
    protected $fillable = [
        'seed_name',
        'special_seed',
        'cares_per_level',
        'harvest_reward',
    ];

    protected function casts(): array
    {
        return [
            'special_seed' => 'boolean',
        ];
    }

    public function trees(): HasMany
    {
        return $this->hasMany(Tree::class);
    }
}
