<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Casillero extends Model
{
    protected $fillable = ['codigo', 'estado'];

    public function acceso()
    {
        return $this->hasOne(Acceso::class)
            ->where('estado', 'en_curso');
    }
}
