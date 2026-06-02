<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActividadProgramada extends Model
{
    protected $table = 'actividades_programadas';

    protected $fillable = [
        'actividad_id',
        'locacion_id',
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'estado'
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }

    public function locacion()
    {
        return $this->belongsTo(Locacion::class);
    }
}
