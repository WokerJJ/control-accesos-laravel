<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramaAcademico extends Model
{
    protected $table = 'programas_academicos';

    protected $fillable = [
        'codigo',
        'nombre',
        'tipo',
        'estado',
    ];

    // ─── Relaciones ──────────────────────────────────
    public function personas(): HasMany
    {
        return $this->hasMany(Persona::class, 'programa_academico_id');
    }

    // ─── Scopes ──────────────────────────────────────
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeCarreras($query)
    {
        return $query->where('tipo', 'carrera');
    }

    public function scopeNoCarreras($query)
    {
        return $query->where('tipo', '!=', 'carrera');
    }

    // ─── Helpers ─────────────────────────────────────
    public function esCarrera(): bool
    {
        return $this->tipo === 'carrera';
    }

    public function esActivo(): bool
    {
        return $this->estado === 'activo';
    }

    // ─── Static helpers ───────────────────────────────

    /** Map tipo de programa a label legible */
    public static function areaLabel(?string $tipo): string
    {
        return match ($tipo) {
            'carrera'       => 'Estudiante',
            'profesor'      => 'Profesor',
            'administrativo'=> 'Administrativo',
            'externo'       => 'Externo',
            default         => '—',
        };
    }

    /** Color de badge para cada área */
    public static function areaColor(?string $tipo): string
    {
        return match ($tipo) {
            'carrera'       => 'primary',
            'profesor'      => 'success',
            'administrativo'=> 'warning',
            'externo'       => 'info',
            default         => 'secondary',
        };
    }

    /** Map frontend area value to tipo value */
    public static function tipoFromArea(string $area): ?string
    {
        return match ($area) {
            'estudiante'     => 'carrera',
            'profesor'       => 'profesor',
            'administrativo' => 'administrativo',
            'externo'        => 'externo',
            default          => null,
        };
    }
}
