<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Actividad extends Model
{
    protected $table = 'actividades';

    protected $fillable = [
        'tipo_actividad_id',
        'locacion_id',

        'nombre',
        'descripcion',

        'tipo',

        'fecha_inicio',
        'fecha_fin',
        'hora_inicio',
        'hora_fin',

        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function tipoActividad(): BelongsTo
    {
        return $this->belongsTo(TipoActividad::class);
    }

    public function locacion(): BelongsTo
    {
        return $this->belongsTo(Locacion::class);
    }

    public function accesos(): HasMany
    {
        return $this->hasMany(Acceso::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function estaEnCurso(): bool
    {
        if (!$this->fecha_inicio || !$this->fecha_fin) {
            return false;
        }

        return now()->between(
            $this->fecha_inicio,
            $this->fecha_fin
        );
    }

    public function estaFinalizada(): bool
    {
        return $this->fecha_fin &&
            now()->greaterThan($this->fecha_fin);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeProgramadas($query)
    {
        return $query->where('tipo', 'programada');
    }

    public function scopeInstantaneas($query)
    {
        return $query->where('tipo', 'instantanea');
    }

    public function scopePersonalizadas($query)
    {
        return $query->where('tipo', 'personalizada');
    }

    public function scopeEnCurso($query)
    {
        return $query->where('estado', 'en_curso');
    }

    public function scopeActivas($query)
    {
        return $query->whereIn('estado', [
            'programada',
            'en_curso'
        ]);
    }
}
