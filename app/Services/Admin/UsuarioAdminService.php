<?php

namespace App\Services\Admin;

use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UsuarioAdminService
{
    /** KPIs superiores */
    public function obtenerStats(): array
    {
        return [
            'total'      => Persona::whereHas('usuario')->count(),
            'nuevos_mes' => Persona::whereHas('usuario')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            // Extras útiles
            'activos'    => Persona::whereHas('usuario', fn($q) => $q->where('estado', 'activo'))->count(),
            'inactivos'  => Persona::whereHas('usuario', fn($q) => $q->where('estado', 'inactivo'))->count(),
        ];
    }

    /** Tabla principal */
    public function obtenerListado(Request $request): LengthAwarePaginator
    {
        return Persona::query()
            ->select([
                'id', 'doc_identidad',
                'primer_nombre', 'segundo_nombre',
                'primer_apellido', 'segundo_apellido',
                'email', 'celular',
                'municipio_id', 'created_at',
            ])
            ->with([
                'usuario:id,persona_id,rol_id,ultimo_acceso,estado',
                'usuario.rol:id,nombre_rol',        // ← para mostrar el nombre del rol
            ])
            ->withCount('accesos')              // ← total histórico de ingresos

            // ── Filtros ───────────────────────────────────
            ->when($request->buscar, function ($q, $buscar) {
                $q->where(function ($q) use ($buscar) {
                    $q->where('doc_identidad',    'like', "%{$buscar}%")
                        ->orWhere('primer_nombre',  'like', "%{$buscar}%")
                        ->orWhere('primer_apellido','like', "%{$buscar}%")
                        ->orWhere('email',          'like', "%{$buscar}%");
                });
            })
            ->when($request->rol, fn($q, $rol) =>
            $q->whereHas('usuario', fn($q) => $q->where('rol_id', $rol))
            )
            ->when($request->estado, fn($q, $estado) =>
            $q->whereHas('usuario', fn($q) => $q->where('estado', $estado))
            )
            ->when($request->registro, function ($q, $registro) {
                match ($registro) {
                    'hoy'  => $q->whereDate('created_at', today()),
                    'mes'  => $q->whereMonth('created_at', now()->month)
                        ->whereYear('created_at', now()->year),
                    'anio' => $q->whereYear('created_at', now()->year),
                    default => null,
                };
            })

            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn($persona) => (object) [
                'id'             => $persona->id,
                'usuario_id'     => $persona->usuario?->id,
                'nombre_completo'=> $persona->nombre_completo,
                'doc_identidad'  => $persona->doc_identidad,
                'celular'        => $persona->celular,           // ← corregido
                'email'          => $persona->email,
                'rol'            => $persona->usuario?->rol?->nombre_rol ?? '—',
                'estado'         => $persona->usuario?->estado ?? 'inactivo',
                'activo'         => $persona->usuario?->estado === 'activo',
                'ultimo_acceso'  => $persona->usuario?->ultimo_acceso
                        ?->format('d/m/Y H:i') ?? 'Sin accesos',
                'total_accesos'  => $persona->accesos_count,
                'created_at'     => $persona->created_at,
            ]);
    }

    public function obtenerDetalle(int $personaId): object
    {
        $persona = Persona::query()
            ->with([
                'usuario:id,persona_id,rol_id,ultimo_acceso,estado',
                'usuario.rol:id,nombre_rol',
                'municipio:id,nombre,departamento_id',
                'municipio.departamento:id,nombre',
            ])
            ->withCount('accesos')
            ->findOrFail($personaId);

        return (object) [
            'id'              => $persona->id,
            'usuario_id'      => $persona->usuario?->id,
            'nombre_completo' => $persona->nombre_completo,
            'doc_identidad'   => $persona->doc_identidad,
            'email'           => $persona->email,
            'celular'         => $persona->celular,
            'direccion'       => $persona->direccion,
            'municipio'       => $persona->municipio?->nombre,
            'departamento'    => $persona->municipio?->departamento?->nombre,
            'municipio_id'    => $persona->municipio_id,
            'rol'             => $persona->usuario?->rol?->nombre_rol ?? '—',
            'rol_id'          => $persona->usuario?->rol_id,
            'estado'          => $persona->usuario?->estado ?? 'inactivo',
            'ultimo_acceso'   => $persona->usuario?->ultimo_acceso?->format('d/m/Y H:i') ?? 'Sin accesos',
            'total_accesos'   => $persona->accesos_count,
            'fecha_registro'  => $persona->fecha_registro,
            'created_at'      => $persona->created_at,
        ];
    }

    public function actualizar(int $personaId, array $datos): void
    {
        $persona = Persona::findOrFail($personaId);

        // Actualizar persona
        $persona->update([
            'email'       => $datos['email']       ?? $persona->email,
            'celular'     => $datos['celular']      ?? $persona->celular,
            'direccion'   => $datos['direccion']    ?? $persona->direccion,
            'municipio_id'=> $datos['municipio_id'] ?? $persona->municipio_id,
        ]);

        // Actualizar usuario (rol + estado)
        $persona->usuario?->update([
            'rol_id' => $datos['rol_id'],
            'estado' => $datos['estado'],
        ]);
    }
}
