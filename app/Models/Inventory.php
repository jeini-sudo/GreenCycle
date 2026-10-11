<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Inventory extends Model
{
    //
    protected $fillable = [
        'quantity',
       
    ];

     public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

     public function shop_items(): BelongsTo
    {
        return $this->belongsTo(Shop_item::class);
    }
}
