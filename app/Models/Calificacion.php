<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Calificacion extends Model
{
    use HasFactory;

    protected $table = 'calificaciones';

    protected $fillable = [
        'acceso_id',
        'servicio',
        'atencion',
        'lugar',
        'calidad',
        'comentario',
    ];

    protected $casts = [
        'servicio' => 'integer',
        'atencion' => 'integer',
        'lugar'    => 'integer',
        'calidad'  => 'integer',
    ];

    // ─── Relaciones ──────────────────────────────────

    public function acceso()
    {
        return $this->belongsTo(Acceso::class);
    }

    // ─── Scopes ──────────────────────────────────────

    public function scopeDeHoy($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeDelMes($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    // ─── Consultas estáticas ─────────────────────────

    /**
     * Verifica si un acceso ya fue calificado
     */
    public static function existeParaAcceso(int $accesoId): bool
    {
        return static::where('acceso_id', $accesoId)->exists();
    }

    /**
     * Promedio general de todos los campos
     */
    public static function promedioGeneral(): float
    {
        return round(
            static::avg(
                \DB::raw('(servicio + atencion + lugar + calidad) / 4')
            ) ?? 0,
            1
        );
    }

    /**
     * Promedio por campo individual
     */
    public static function promediosPorCampo(): array
    {
        return [
            'servicio' => round(static::avg('servicio') ?? 0, 1),
            'atencion' => round(static::avg('atencion') ?? 0, 1),
            'lugar'    => round(static::avg('lugar')    ?? 0, 1),
            'calidad'  => round(static::avg('calidad')  ?? 0, 1),
        ];
    }

    // ─── Accessors ───────────────────────────────────

    /**
     * Promedio de esta calificación individual
     */
    public function getPromedioAttribute(): float
    {
        return round(
            ($this->servicio + $this->atencion + $this->lugar + $this->calidad) / 4,
            1
        );
    }
}
