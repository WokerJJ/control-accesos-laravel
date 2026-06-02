<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'personas';

    protected $fillable = [
        'tipo_identificacion_id', 'doc_identidad', 'primer_nombre',
        'segundo_nombre', 'primer_apellido', 'segundo_apellido',
        'email', 'celular', 'plan', 'direccion',
        'municipio_id', 'estado', 'fecha_registro'
    ];

    protected $casts = [
        'fecha_registro' => 'date'
    ];

    // ─── Relaciones ──────────────────────────────────
    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'persona_id');
    }

    public function accesos()
    {
        return $this->hasMany(Acceso::class);
    }

    public function municipio(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Municipio::class);
    }

    // ─── Accessors ───────────────────────────────────
    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->primer_nombre} {$this->segundo_nombre} {$this->primer_apellido} {$this->segundo_apellido}");
    }

    // ─── Scopes ──────────────────────────────────────
    public function scopeActiva($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeParaIngreso($query)
    {
        return $query->select([
            'id',
            'primer_nombre',
            'segundo_nombre',
            'primer_apellido',
            'segundo_apellido',
            'doc_identidad',
            'estado',
        ]);
    }

    // ─── Consultas estáticas ─────────────────────────

    public static function buscarPorDocumento(string $doc): ?self
    {
        return static::where('doc_identidad', $doc)
            ->where('estado', 'activo')
            ->first();
    }

    public static function buscarPorDocumentoRol(string $doc): ?self
    {
        return static::with('usuario.rol')
            ->where('doc_identidad', $doc)
            ->where('estado', 'activo')
            ->first();
    }

    // ─── Helpers ─────────────────────────────────────
    public function accesoActivo(): ?Acceso
    {
        return $this->accesos()
            ->where('estado', 'en_curso')
            ->latest()
            ->first();
    }
}
