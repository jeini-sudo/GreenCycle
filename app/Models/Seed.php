<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\SeedFactory;

class Seed extends Model
{
    /** @use HasFactory<SeedFactory> */
    use HasFactory;

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
