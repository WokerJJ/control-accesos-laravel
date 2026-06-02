<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoActividad extends Model
{
    protected $table = 'tipos_actividad';

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria',
        'activa',
    ];

    protected $casts = [
        'requiere_casillero' => 'boolean',
        'activa' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function actividades(): HasMany
    {
        return $this->hasMany(Actividad::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopeInstantaneas($query)
    {
        return $query->where('categoria', 'instantanea');
    }

    public function scopeProgramables($query)
    {
        return $query->where('categoria', 'programable');
    }
}
