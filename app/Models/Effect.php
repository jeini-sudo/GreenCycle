<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Effect extends Model
{
    // Vacío a propósito: un efecto lo crea el servidor al usar un ítem
    // (con sus fechas de inicio y expiración), nunca el cliente.
    protected $fillable = [];

    protected function casts(): array
    {
        return [
            'started_time' => 'datetime',
            'expires_at' => 'datetime',
            'status' => 'boolean',
        ];
    }

    public function shopItem(): BelongsTo
    {
        return $this->belongsTo(ShopItem::class);
    }

    public function tree(): BelongsTo
    {
        return $this->belongsTo(Tree::class);
    }
}
