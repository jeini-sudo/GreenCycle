<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;



class Purchases_history extends Model
{
    //

     protected $fillable = [

        'quantity',
       
    ];

    protected function casts(): array
    {
        return [
    
        'purchased_time' => 'datetime',

       ];

    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


     public function shop_items(): BelongsTo
    {
        return $this->belongsTo(Shop_item::class);
    }
}
