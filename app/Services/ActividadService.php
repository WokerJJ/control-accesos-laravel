<?php

namespace App\Services;

use App\Models\Actividad;
use App\Models\Persona;
use App\Models\TipoActividad;
use App\Models\Locacion;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ActividadService
{
    // ═══════════════════════════════════════════════
    // Configuración visual
    // Estados DB: en_curso, pendiente, finalizada, cancelada
    // Estados visuales: En curso, Pendientes, Finalizada, Cancelada
    // ═══════════════════════════════════════════════

    private const COLORES_ESTADO = [
        'En curso'   => 'success',
        'Pendiente' => 'warning',
        'Finalizada' => 'secondary',
        'Cancelada'  => 'danger',
    ];

    private const ICONOS_ESTADO = [
        'En curso'   => 'fas fa-play-circle',
        'Pendiente'  => 'fas fa-hourglass-half',
        'Finalizada' => 'fas fa-check-circle',
        'Cancelada'  => 'fas fa-times-circle',
    ];

    /**
     * Mapa de iconos por palabra clave en el nombre
     * Organizado por categorías para fácil mantenimiento
     */
    private const ICONOS_ACTIVIDAD = [
        // ── Préstamos y devoluciones ──
        'préstamo'      => 'fas fa-hand-holding-open',
        'prestamo'      => 'fas fa-hand-holding-open',
        'devolución'    => 'fas fa-undo-alt',
        'devolucion'    => 'fas fa-undo-alt',
        'renovación'    => 'fas fa-sync-alt',
        'renovacion'    => 'fas fa-sync-alt',

        // ── Estudio ──
        'estudio'       => 'fas fa-user-graduate',
        'individual'    => 'fas fa-user',
        'grupal'        => 'fas fa-users',
        'grupo'         => 'fas fa-users',

        // ── Lectura ──
        'lectura'       => 'fas fa-book-open',
        'libro'         => 'fas fa-book',
        'referencia'    => 'fas fa-bookmark',

        // ── Tecnología ──
        'computador'    => 'fas fa-desktop',
        'computadora'   => 'fas fa-desktop',
        'cómputo'       => 'fas fa-desktop',
        'computo'       => 'fas fa-desktop',
        'impresión'     => 'fas fa-print',
        'impresion'     => 'fas fa-print',
        'escaneo'       => 'fas fa-scanner',
        'bases de datos'=> 'fas fa-database',
        'internet'      => 'fas fa-wifi',
        'sistema'       => 'fas fa-laptop-code',

        // ── Tutorías y clases ──
        'tutoría'       => 'fas fa-chalkboard-teacher',
        'tutoria'       => 'fas fa-chalkboard-teacher',
        'clase'         => 'fas fa-chalkboard',
        'laboratorio'   => 'fas fa-flask',
        'práctica'      => 'fas fa-tools',
        'practica'      => 'fas fa-tools',

        // ── Eventos académicos ──
        'conferencia'   => 'fas fa-microphone-alt',
        'seminario'     => 'fas fa-graduation-cap',
        'taller'        => 'fas fa-hammer',
        'workshop'      => 'fas fa-laptop',
        'capacitación'  => 'fas fa-certificate',
        'capacitacion'  => 'fas fa-certificate',
        'jornada'       => 'fas fa-calendar-day',
        'encuentro'     => 'fas fa-handshake',
        'firma'         => 'fas fa-pen-nib',

        // ── Investigación ──
        'investigación' => 'fas fa-microscope',
        'investigacion' => 'fas fa-microscope',
        'tesis'         => 'fas fa-scroll',
        'proyecto'      => 'fas fa-project-diagram',

        // ── Administrativo ──
        'gestión'       => 'fas fa-clipboard-list',
        'gestion'       => 'fas fa-clipboard-list',
        'administrativa'=> 'fas fa-file-alt',
        'calificación'  => 'fas fa-tasks',
        'calificacion'  => 'fas fa-tasks',
        'preparación'   => 'fas fa-edit',
        'preparacion'   => 'fas fa-edit',

        // ── Recreación ──
        'recreat'       => 'fas fa-smile',
        'descanso'      => 'fas fa-couch',
        'ocio'          => 'fas fa-gamepad',
    ];

    // ═══════════════════════════════════════════════
    // API pública — visual
    // ═══════════════════════════════════════════════

    public function colorPorEstado(string $estado): string
    {
        return self::COLORES_ESTADO[$estado] ?? 'primary';
    }

    public function iconoPorEstado(string $estado): string
    {
        return self::ICONOS_ESTADO[$estado] ?? 'fas fa-circle';
    }

    public function visualPorEstado(string $estado): array
    {
        return [
            'color' => $this->colorPorEstado($estado),
            'icono' => $this->iconoPorEstado($estado),
        ];
    }

    public function iconoPorNombre(string $nombre): string
    {
        $lower = strtolower($nombre);

        foreach (self::ICONOS_ACTIVIDAD as $clave => $icono) {
            if (str_contains($lower, $clave)) {
                return $icono;
            }
        }

        return 'fas fa-star';
    }

    // ═══════════════════════════════════════════════
    // Consultas
    // ═══════════════════════════════════════════════

    public function obtenerPersonaIngreso(int $personaId): Persona
    {
        return Persona::query()
            ->select('id', 'primer_nombre', 'segundo_nombre',
                'primer_apellido', 'segundo_apellido', 'doc_identidad')
            ->findOrFail($personaId);
    }

    public function obtenerTiposActividad(): Collection
    {
        return TipoActividad::activas()->orderBy('nombre')->get();
    }

    public function obtenerLocaciones(): Collection
    {
        return Locacion::activas()->orderBy('nombre')->get();
    }

    /**
     * Fijas — siempre disponibles (no canceladas)
     */
    public function obtenerFijas(int $limite = 8): Collection
    {
        return Actividad::query()
            ->select('id', 'nombre', 'descripcion', 'tipo_actividad_id', 'locacion_id')
            ->with(['locacion:id,nombre'])
            ->where('tipo', 'fija')
            ->where('estado', '!=', 'cancelada') // ← fix: no filtra 'activa'
            ->orderBy('nombre')
            ->limit($limite)
            ->get();
    }

    public function obtenerPendientes(int $limite = 6): Collection
    {
        $ahora      = now('America/Bogota');
        $hoy        = $ahora->toDateString();
        $horaActual = $ahora->format('H:i:s');

        return Actividad::query()
            ->select([
                'id', 'nombre', 'descripcion', 'tipo_actividad_id',
                'locacion_id', 'fecha_inicio', 'fecha_fin',
                'hora_inicio', 'hora_fin', 'estado',
            ])
            ->with(['locacion:id,nombre'])
            ->where('tipo', 'programada')
            ->where('estado', '!=', 'cancelada')
            ->where(function ($q) use ($hoy, $horaActual) {
                $q->whereDate('fecha_inicio', '>', $hoy)
                    ->orWhere(function ($q2) use ($hoy, $horaActual) {
                        $q2->whereDate('fecha_inicio', $hoy)
                            ->whereTime('hora_inicio', '>', $horaActual);
                    });
            })
            ->orderBy('fecha_inicio')
            ->orderBy('hora_inicio')
            ->limit($limite)
            ->get();
    }

    /**
     * Programadas y personalizadas en curso ahora
     */
    public function obtenerEnCurso(int $limite = 6): Collection
    {
        $ahora      = now('America/Bogota');
        $hoy        = $ahora->toDateString();
        $horaActual = $ahora->format('H:i:s');

        return Actividad::query()
            ->select(['id', 'nombre', 'descripcion', 'tipo_actividad_id',
                'locacion_id', 'tipo', 'fecha_inicio', 'fecha_fin',
                'hora_inicio', 'hora_fin'])
            ->with(['locacion:id,nombre'])
            ->whereNotIn('estado', ['cancelada', 'finalizada'])
            ->where(function ($query) use ($hoy, $horaActual, $ahora) {

                // Programadas en curso hoy
                $query->where(function ($q) use ($hoy, $horaActual) {
                    $q->where('tipo', 'programada')
                        ->whereDate('fecha_inicio', '<=', $hoy)
                        ->whereDate('fecha_fin', '>=', $hoy)
                        ->whereTime('hora_inicio', '<=', $horaActual)
                        ->whereTime('hora_fin', '>=', $horaActual);
                });
            })
            ->orderByRaw("FIELD(tipo, 'programada')")
            ->orderBy('hora_inicio')
            ->limit($limite)
            ->get();
    }

    public function obtenerEventosCalendario(): Collection
    {
        return Actividad::query()
            ->with(['locacion:id,nombre'])
            ->where('tipo', 'programada')
            ->orderBy('fecha_inicio')
            ->get()
            ->map(function ($actividad) {

                $estadoVisual = $this->obtenerEstadoVisual($actividad);

                return [
                    'id'    => $actividad->id,
                    'title' => $actividad->nombre,

                    // FullCalendar maneja el rango multi-día solo
                    'start' => $actividad->fecha_inicio->format('Y-m-d')
                        . 'T' . $actividad->hora_inicio,

                    'end'   => $actividad->fecha_fin->format('Y-m-d')
                        . 'T' . $actividad->hora_fin,

                    'extendedProps' => [
                        'descripcion'       => $actividad->descripcion,
                        'tipo_actividad_id' => $actividad->tipo_actividad_id,  // ← agregar
                        'locacion_id'       => $actividad->locacion_id,        // ← agregar
                        'locacion'          => $actividad->locacion?->nombre,
                        'fecha_fin'         => $actividad->fecha_fin->format('Y-m-d'), // ← agregar
                        'estado'            => $estadoVisual,
                        'estado_db'         => $actividad->estado,
                        'color'             => $this->colorPorEstado($estadoVisual),
                        'icono'             => $this->iconoPorEstado($estadoVisual),
                        'hora_inicio'       => $actividad->hora_inicio,
                        'hora_fin'          => $actividad->hora_fin,
                    ],
                ];
            });
    }

    public function resumen(): array
    {
        $actividades = Actividad::where('tipo', 'programada')
            ->whereNotIn('estado', ['cancelada'])
            ->get();

        $enCurso    = 0;
        $pendientes = 0;
        $finalizadas = 0;

        foreach ($actividades as $actividad) {
            match($this->obtenerEstadoVisual($actividad)) {
                'En curso'  => $enCurso++,
                'Pendiente' => $pendientes++,
                'Finalizada' => $finalizadas++,
                default     => null,
            };
        }

        return [
            'total' => [
                'valor' => $actividades->count(),
                'color' => 'info',
                'icono' => 'fas fa-list',
            ],
            'en_curso' => [
                'valor' => $enCurso,
                'color' => $this->colorPorEstado('En curso'),
                'icono' => $this->iconoPorEstado('En curso'),
            ],
            'finalizadas' => [
                'valor' => $finalizadas,
                'color' => $this->colorPorEstado('Finalizada'),
                'icono' => $this->iconoPorEstado('Finalizada'),
            ],
            'pendientes' => [
                'valor' => $pendientes,
                'color' => $this->colorPorEstado('Pendiente'),
                'icono' => $this->iconoPorEstado('Pendiente'),
            ],
        ];
    }

    public function crearProgramada(array $datos): Actividad
    {
        return Actividad::create([
            'tipo_actividad_id' => $datos['tipo_actividad_id'],
            'nombre'            => $datos['nombre'],
            'descripcion'       => $datos['descripcion'],
            'locacion_id'       => $datos['locacion_id'],
            'tipo'              => 'programada',
            'estado' => $this->calcularEstadoInicial($datos['fecha_inicio'], $datos['hora_inicio']),
            'fecha_inicio'      => $datos['fecha_inicio'],
            'fecha_fin'         => $datos['fecha_fin'],
            'hora_inicio'       => $datos['hora_inicio'] . ':00',
            'hora_fin'          => $datos['hora_fin'] . ':00',
        ]);
    }

    public function actualizarProgramada(Actividad $actividad, array $datos): Actividad
    {
        $estado = $this->calcularEstadoInicial($datos['fecha_inicio'], $datos['hora_inicio']);

        $actividad->update([
            'tipo_actividad_id' => $datos['tipo_actividad_id'],
            'nombre'            => $datos['nombre'],
            'descripcion'       => $datos['descripcion'],
            'locacion_id'       => $datos['locacion_id'],
            'estado'            => $estado,
            'fecha_inicio'      => $datos['fecha_inicio'],
            'fecha_fin'         => $datos['fecha_fin'],
            'hora_inicio'       => \Carbon\Carbon::createFromFormat('H:i', $datos['hora_inicio'])->format('H:i:s'),
            'hora_fin'          => \Carbon\Carbon::createFromFormat('H:i', $datos['hora_fin'])->format('H:i:s'),
        ]);

        return $actividad;
    }

    public function cancelarProgramada(Actividad $actividad): void
    {
        // Soft-cancel: marca cancelada, no elimina el registro
        $actividad->update(['estado' => 'cancelada']);
    }

    /**
     * Calcula el estado DB inicial al crear una actividad programada.
     * Compara el inicio con la hora actual en America/Bogota.
     *
     * @param  string $fechaInicio  'Y-m-d'
     * @param  string $horaInicio   'H:i' o 'H:i:s'
     * @return string               'pendiente' | 'en_curso'
     */
    private function calcularEstadoInicial(string $fechaInicio, string $horaInicio): string
    {
        $inicio = Carbon::parse("$fechaInicio $horaInicio", 'America/Bogota');
        return now('America/Bogota')->lt($inicio) ? 'pendiente' : 'en_curso';
    }

    // ═══════════════════════════════════════════════
    // Helpers privados
    // ═══════════════════════════════════════════════

    public function obtenerEstadoVisual(Actividad $actividad): string
    {
        if ($actividad->estado === 'cancelada') return 'Cancelada';

        $ahora  = now('America/Bogota');
        $inicio = Carbon::parse(
            $actividad->fecha_inicio->format('Y-m-d') . ' ' . $actividad->hora_inicio,
            'America/Bogota'
        );
        $fin = Carbon::parse(
            $actividad->fecha_fin->format('Y-m-d') . ' ' . $actividad->hora_fin,
            'America/Bogota'
        );

        if ($ahora->lt($inicio)) return 'Pendiente';   // ← era 'Programada'
        if ($ahora->gt($fin))    return 'Finalizada';

        return 'En curso';
    }
}
