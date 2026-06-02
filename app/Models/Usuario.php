<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Usuario extends Model implements Authenticatable
{
    use AuthenticatableTrait;

    protected $table = 'usuarios';

    protected $fillable = [
        'persona_id',
        'rol_id',
        'password_hash',
        'ultimo_acceso',
        'estado'
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'ultimo_acceso' => 'datetime'
    ];

    // ─────────────────────────────
    // AUTH
    // ─────────────────────────────

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // ─────────────────────────────
    // RELACIONES
    // ─────────────────────────────

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    // ─────────────────────────────
    // ACCESSORS
    // ─────────────────────────────

    public function getNombreCompletoAttribute(): ?string
    {
        return $this->persona?->nombre_completo;
    }

    // ─────────────────────────────
    // HELPERS
    // ─────────────────────────────

    public function esActivo(): bool
    {
        return $this->estado === 'activo';
    }

    public function verificarPassword(string $password): bool
    {
        return Hash::check($password, $this->password_hash);
    }

    // ─────────────────────────────
    // MUTATORS
    // ─────────────────────────────

    public function setPasswordHashAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password_hash'] =
                strlen($value) === 60 ? $value : Hash::make($value);
        }
    }
}
