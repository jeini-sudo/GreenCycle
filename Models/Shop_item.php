<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Shop_item extends Model
{
    //
     protected $fillable = [
        'name',
        'prize',
        'description'
    ];

    public function purchases_histories(): HasMany
    {
        return $this->hasMany(Purchases_history::class);
    }
    
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function effects(): HasMany
    {
        return $this->hasMany(Effect::class);
    }
}
