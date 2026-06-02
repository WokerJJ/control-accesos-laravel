<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Acceso extends Model
{
    use HasFactory;

    protected $table = 'accesos';

    protected $fillable = [
        'persona_id',
        'actividad_id',
        'casillero_id',
        'locacion_id',
        'hora_ingreso',
        'hora_salida',
        'duracion',
        'estado',
        'metodo_acceso',
    ];

    protected $casts = [
        'hora_ingreso' => 'datetime',
        'hora_salida' => 'datetime',
        'duracion' => 'integer',
    ];

    // ─── Relaciones ──────────────────────────────────
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function actividad()
    {
        return $this->belongsTo(Actividad::class);
    }

    public function casillero()
    {
        return $this->belongsTo(Casillero::class);
    }

    public function locacion()
    {
        return $this->belongsTo(Locacion::class);
    }

    // ─── Scopes ──────────────────────────────────────

    /**
     * Scope para filtrar accesos en curso
     * Uso: Acceso::activos()->get() o Acceso::activos()->count()
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'en_curso');
    }

    // ─── Accessors ───────────────────────────────────

    /**
     * Formatea la duración en horas y minutos
     */
    public function getDuracionFormateadaAttribute(): ?string
    {
        if (!$this->duracion) {
            return null;
        }

        $horas = floor($this->duracion / 60);
        $minutos = $this->duracion % 60;

        if ($horas > 0) {
            return "{$horas}h {$minutos}m";
        }

        return "{$minutos}m";
    }
}
