<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;


//coins o pensarlo como wallet
#[Fillable(['name', 'email', 'password', 'coins'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    // un usuario puede tener muchos arboles, 
    // pero un arbol solo puede pertenecer a un usuario 
    public function trees(): HasMany
    {
        return $this->hasMany(Tree::class);
    }


    // un usuario puede tener muchas compras,
    // pero una compra solo puede pertenecer a un usuario
    public function purchaseHistories(): HasMany
    {
        return $this->hasMany(PurchaseHistory::class);
    }

    // quiza debería cambiarse el nombre por algo como "user_items"
    // la tabla lo que registra son: item, usuario dueño del item, 
    // y cantidad de ese item. Son multiples registros porque son distintos items
    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }
}
