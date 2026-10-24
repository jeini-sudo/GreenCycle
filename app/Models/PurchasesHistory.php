<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchasesHistory extends Model
{
    // Vacío a propósito: una compra la arma el servidor (precio real del catálogo,
    // monedas descontadas, fecha). Se crea con PurchasesHistory::forceCreate([...])
    // dentro de la transacción de compra, nunca con datos del cliente.
    protected $fillable = [];

    protected function casts(): array
    {
        return [
            'purchased_time' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shopItem(): BelongsTo
    {
        return $this->belongsTo(ShopItem::class);
    }
}
