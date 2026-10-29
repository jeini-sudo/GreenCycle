<?php

namespace App\Models;

//no he puesto los imports porque el código está sin terminar
use Illuminate\Database\Eloquent\Model;


class Tree extends Model
{
    //solo lleva el id de semilla, puesto que es no lleva otros atributos que se deban asignar aquí, aún así se llena automáticamente, no por el usuario
    protected $fillable=[
        'seed_id',
    ];

    //son los atributes que van predefinidos y se van actualizando según el checkeo del cuidado
    protected $attributes = [
        'level'=> 0,
        'progress'=> 0,
        'health'=> 100,
        'status'=> 'alive',
    ];

    protected function casts(){
        return[
            'last_cared_at'=>'datetime',
            'next_cared_at'=>'datetime',
            'last_decay_at'=>'date',
            'harvested_at'=>'datetime',
        ];
    }


    //es del usuario que plantó el árbol
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    //es de la semilla de la que el árbol
    public function seed(): BelongsTo
    {
        return $this->belongsTo(Seed::class);
    }

    //los efectos que puede tener, el has many es porque ouede tener varios a la vez
    public function effects(): HasMany
    {
        return $this->hasMany(TreeEffects::class);
    }
}
