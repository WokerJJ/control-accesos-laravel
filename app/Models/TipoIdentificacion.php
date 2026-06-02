<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoIdentificacion extends Model
{
    protected $table = 'tipo_identificacion';

    protected $fillable = [
        'abreviatura',
        'descripcion',
    ];

    public static function opciones()
    {
        return self::select('id', 'abreviatura')->get();
    }
}
