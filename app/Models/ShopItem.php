<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShopItem extends Model
{
    
    protected $fillable = [
        'name',
        'prize',
        'description',
    ];

    public function purchasesHistories(): HasMany
    {
        return $this->hasMany(PurchasesHistory::class);
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
