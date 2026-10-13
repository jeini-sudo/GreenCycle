<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Tree extends Model
{
    //
    protected $fillable = [
        'status',

        
    ];

     protected function casts(): array
    {
        return [
    
        'last_care_at' => 'datetime',
            'next_care_at' => 'datetime',
            'last_decay_at' => 'datetime',
            'harvested_at' => 'datetime',

       ];
    }

    public function seeds(): BelongsTo
    {
        return $this->belongsTo(Seed::class);
    }

   public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
}
