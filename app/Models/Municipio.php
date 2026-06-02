<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo Municipio
 *
 * @property int    $id
 * @property string $nombre
 * @property int    $departamento_id
 */
class Municipio extends Model
{
    protected $fillable = ['nombre', 'departamento_id'];

    protected $table = 'municipio';

    // ── Relaciones ────────────────────────────────

    /** Departamento al que pertenece */
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class);
    }

    /** Personas registradas en este municipio */
    public function personas(): HasMany
    {
        return $this->hasMany(Persona::class);
    }

    // ── Scopes ────────────────────────────────────

    /** Filtra municipios por departamento */
    public function scopeDelDepartamento($query, int $departamentoId)
    {
        return $query->where('departamento_id', $departamentoId);
    }
}
