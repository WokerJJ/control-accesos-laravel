<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccesoOlvidado extends Model
{
    protected $table = 'accesos_olvidados';

    protected $fillable = [
        'acceso_id',
        'hora_cierre_forzado',
        'motivo',
    ];

    protected $casts = [
        'hora_cierre_forzado' => 'datetime',
    ];

    /**
     * Acceso original
     */
    public function acceso(): BelongsTo
    {
        return $this->belongsTo(Acceso::class);
    }

    /**
     * Persona involucrada
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    /**
     * Actividad asociada
     */
    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class);
    }

    /**
     * Casillero que tenía asignado
     */
    public function casillero(): BelongsTo
    {
        return $this->belongsTo(Casillero::class);
    }
}
