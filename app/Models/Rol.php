<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'nombre_rol',
        'descripcion',
        'estado'
    ];

    /**
     * Relación: un rol tiene muchos usuarios
     */
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'rol_id');
    }

    /**
     * Scope: solo roles activos
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Verificar si el rol es administrador
     */
    public function esAdministrador()
    {
        return strtolower($this->nombre_rol) === 'administrador';
    }

    /**
     * Verificar si el rol es operador
     */
    public function esOperador()
    {
        return strtolower($this->nombre_rol) === 'operador';
    }
}
