<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Effect extends Model
{
    //

     protected $fillable = [
        'type',
    
    ];

     protected function casts(): array
    {
        return [
    
        'started_time' => 'datetime',
        'expires_at' => 'datetime',
        'status' => 'boolean',

       ];

    }

     public function shop_items(): BelongsTo
    {
        return $this->belongsTo(Shop_item::class);
    }

     public function trees(): BelongsTo
    {
        return $this->belongsTo(Tree::class);
    }
}
