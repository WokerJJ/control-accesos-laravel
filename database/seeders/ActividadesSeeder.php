<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoActividad;
use App\Models\Actividad;
use App\Models\Locacion;
use Carbon\Carbon;

class ActividadesSeeder extends Seeder
{
    public function run(): void
    {
        $locIds  = Locacion::pluck('id', 'nombre');
        $tipoIds = TipoActividad::pluck('id', 'nombre');
        $ahora   = now('America/Bogota');
        $hoy     = $ahora->toDateString();

        // ── Fijas — sin fechas, estado pendiente ──────────
        $fijas = [
            [
                'tipo_actividad_id' => $tipoIds['Administrativa'],
                'nombre'      => 'Préstamo de Libros',
                'descripcion' => 'Retira hasta 3 libros por 15 días',
                'locacion_id' => $locIds['Zona de Préstamo'],
                'tipo'        => 'fija',
                'estado'      => 'pendiente',
            ],
            [
                'tipo_actividad_id' => $tipoIds['Administrativa'],
                'nombre'      => 'Devolución de Libros',
                'descripcion' => 'Entrega de material bibliográfico prestado',
                'locacion_id' => $locIds['Zona de Préstamo'],
                'tipo'        => 'fija',
                'estado'      => 'pendiente',
            ],
            [
                'tipo_actividad_id' => $tipoIds['Administrativa'],
                'nombre'      => 'Renovación de Préstamo',
                'descripcion' => 'Extiende el plazo de devolución hasta 15 días más',
                'locacion_id' => $locIds['Zona de Préstamo'],
                'tipo'        => 'fija',
                'estado'      => 'pendiente',
            ],
            [
                'tipo_actividad_id' => $tipoIds['Administrativa'],
                'nombre'      => 'Gestión Administrativa',
                'descripcion' => 'Pagos, multas y trámites generales',
                'locacion_id' => $locIds['Zona de Préstamo'],
                'tipo'        => 'fija',
                'estado'      => 'pendiente',
            ],
            [
                'tipo_actividad_id' => $tipoIds['Investigación'],
                'nombre'      => 'Consulta de Material de Referencia',
                'descripcion' => 'Acceso a enciclopedias, diccionarios y material no prestable',
                'locacion_id' => $locIds['Sala de Lectura'],
                'tipo'        => 'fija',
                'estado'      => 'pendiente',
            ],
            [
                'tipo_actividad_id' => $tipoIds['Recreación'],
                'nombre'      => 'Lectura Personal',
                'descripcion' => 'Uso de las instalaciones para lectura libre',
                'locacion_id' => $locIds['Sala de Lectura'],
                'tipo'        => 'fija',
                'estado'      => 'pendiente',
            ],
            [
                'tipo_actividad_id' => $tipoIds['Académica'],
                'nombre'      => 'Estudio Individual',
                'descripcion' => 'Uso de cubículos o mesas para estudio autónomo',
                'locacion_id' => $locIds['Sala Principal'],
                'tipo'        => 'fija',
                'estado'      => 'pendiente',
            ],
            [
                'tipo_actividad_id' => $tipoIds['Académica'],
                'nombre'      => 'Estudio en Grupo',
                'descripcion' => 'Trabajo colaborativo entre estudiantes',
                'locacion_id' => $locIds['Sala Principal'],
                'tipo'        => 'fija',
                'estado'      => 'pendiente',
            ],
            [
                'tipo_actividad_id' => $tipoIds['Tecnología'],
                'nombre'      => 'Uso de Computadores',
                'descripcion' => '2 horas máximo por sesión',
                'locacion_id' => $locIds['Sala de Cómputo'],
                'tipo'        => 'fija',
                'estado'      => 'pendiente',
            ],
            [
                'tipo_actividad_id' => $tipoIds['Tecnología'],
                'nombre'      => 'Impresión y Escaneo',
                'descripcion' => 'Servicio de impresión, fotocopia y escaneo de documentos',
                'locacion_id' => $locIds['Sala de Cómputo'],
                'tipo'        => 'fija',
                'estado'      => 'pendiente',
            ],
            [
                'tipo_actividad_id' => $tipoIds['Tecnología'],
                'nombre'      => 'Acceso a Bases de Datos',
                'descripcion' => 'Consulta de repositorios académicos y revistas indexadas',
                'locacion_id' => $locIds['Sala de Cómputo'],
                'tipo'        => 'fija',
                'estado'      => 'pendiente',
            ],
            [
                'tipo_actividad_id' => $tipoIds['Académica'],
                'nombre'      => 'Tutoría con Profesor',
                'descripcion' => 'Sesión de acompañamiento académico con docente',
                'locacion_id' => $locIds['Sala de Tutorías'],
                'tipo'        => 'fija',
                'estado'      => 'pendiente',
            ],
            [
                'tipo_actividad_id' => $tipoIds['Académica'],
                'nombre'      => 'Tutoría entre Pares',
                'descripcion' => 'Apoyo académico entre estudiantes',
                'locacion_id' => $locIds['Sala de Tutorías'],
                'tipo'        => 'fija',
                'estado'      => 'pendiente',
            ],
            [
                'tipo_actividad_id' => $tipoIds['Recreación'],
                'nombre'      => 'Lectura Recreativa',
                'descripcion' => 'Lectura de novelas, revistas o material de entretenimiento',
                'locacion_id' => $locIds['Sala de Lectura'],
                'tipo'        => 'fija',
                'estado'      => 'pendiente',
            ],
            [
                'tipo_actividad_id' => $tipoIds['Recreación'],
                'nombre'      => 'Descanso en Instalaciones',
                'descripcion' => 'Uso de espacios comunes para pausa o descanso',
                'locacion_id' => $locIds['Sala Principal'],
                'tipo'        => 'fija',
                'estado'      => 'pendiente',
            ],
        ];

        foreach ($fijas as $act) {
            Actividad::create($act);
        }

        // ── Programadas — estados según fecha/hora ────────
        $programadas = [
            // En curso hoy
            [
                'tipo_actividad_id' => $tipoIds['Académica'],
                'nombre'      => 'Conferencia: Inteligencia Artificial',
                'descripcion' => 'Dr. García - Impacto de la IA en educación',
                'locacion_id' => $locIds['Auditorio'],
                'tipo'        => 'programada',
                'estado'      => 'en_curso',
                'fecha_inicio'=> $ahora->copy()->subDays(1)->toDateString(),
                'fecha_fin'   => $ahora->copy()->addDays(1)->toDateString(),
                'hora_inicio' => '08:00',
                'hora_fin'    => '22:00',
            ],
            [
                'tipo_actividad_id' => $tipoIds['Académica'],
                'nombre'      => 'Clase: Programación Web',
                'descripcion' => 'Prof. Sánchez - Laravel y Vue.js',
                'locacion_id' => $locIds['Sala de Cómputo'],
                'tipo'        => 'programada',
                'estado'      => 'en_curso',
                'fecha_inicio'=> $hoy,
                'fecha_fin'   => $ahora->copy()->addDays(2)->toDateString(),
                'hora_inicio' => '07:00',
                'hora_fin'    => '22:00',
            ],
            // Pendiente — empieza en días futuros
            [
                'tipo_actividad_id' => $tipoIds['Recreación'],
                'nombre'      => 'Firma: "El Arte de Pensar"',
                'descripcion' => 'Autor: Carlos Ruiz - Edición especial',
                'locacion_id' => $locIds['Sala de Lectura'],
                'tipo'        => 'programada',
                'estado'      => 'pendiente',
                'fecha_inicio'=> $ahora->copy()->addDays(2)->toDateString(),
                'fecha_fin'   => $ahora->copy()->addDays(3)->toDateString(),
                'hora_inicio' => '16:00',
                'hora_fin'    => '18:00',
            ],
            [
                'tipo_actividad_id' => $tipoIds['Académica'],
                'nombre'      => 'Conferencia: Historia del Arte',
                'descripcion' => 'Dra. Elena Vargas - Arte contemporáneo',
                'locacion_id' => $locIds['Auditorio'],
                'tipo'        => 'programada',
                'estado'      => 'pendiente',
                'fecha_inicio'=> $ahora->copy()->addDays(5)->toDateString(),
                'fecha_fin'   => $ahora->copy()->addDays(5)->toDateString(),
                'hora_inicio' => '11:00',
                'hora_fin'    => '13:00',
            ],
            // Finalizada — ya pasó
            [
                'tipo_actividad_id' => $tipoIds['Académica'],
                'nombre'      => 'Tutoría: Física I',
                'descripcion' => 'Tutor: Ana López - Mecánica básica',
                'locacion_id' => $locIds['Sala de Tutorías'],
                'tipo'        => 'programada',
                'estado'      => 'finalizada',
                'fecha_inicio'=> $ahora->copy()->subDays(3)->toDateString(),
                'fecha_fin'   => $ahora->copy()->subDays(2)->toDateString(),
                'hora_inicio' => '14:00',
                'hora_fin'    => '16:00',
            ],
        ];

        // Históricas — finalizadas
        $nombres = [
            'Seminario de Investigación', 'Taller de Laravel',
            'Capacitación TIC', 'Clase Magistral',
            'Conferencia Académica', 'Jornada de Lectura',
            'Workshop de Programación', 'Entrenamiento de Bases de Datos',
            'Tutoría Intensiva', 'Encuentro Académico',
        ];

        $descripciones = [
            'Actividad académica institucional',
            'Espacio de aprendizaje colaborativo',
            'Sesión especial para estudiantes',
            'Capacitación orientada a competencias',
            'Actividad guiada por docente',
        ];

        foreach (range(1, 15) as $i) {
            $inicio     = $ahora->copy()->subDays(rand(10, 90));
            $duracion   = rand(1, 2);
            $horaInicio = rand(7, 16);
            $horaFin    = min($horaInicio + rand(1, 3), 20);

            $programadas[] = [
                'tipo_actividad_id' => collect($tipoIds)->random(),
                'nombre'            => collect($nombres)->random(),
                'descripcion'       => collect($descripciones)->random(),
                'locacion_id'       => collect($locIds)->random(),
                'tipo'              => 'programada',
                'estado'            => 'finalizada', // ← históricas siempre finalizadas
                'fecha_inicio'      => $inicio->toDateString(),
                'fecha_fin'         => $inicio->copy()->addDays($duracion)->toDateString(),
                'hora_inicio'       => str_pad($horaInicio, 2, '0', STR_PAD_LEFT) . ':00',
                'hora_fin'          => str_pad($horaFin, 2, '0', STR_PAD_LEFT) . ':00',
            ];
        }

        foreach ($programadas as $act) {
            Actividad::create($act);
        }

        // ── Personalizadas — en curso ahora ───────────────
        $personalizadas = [
            [
                'tipo_actividad_id' => $tipoIds['Académica'],
                'nombre'      => 'Tutoría: Álgebra Lineal',
                'descripcion' => 'Estudiante ayuda a compañeros con matrices',
                'locacion_id' => $locIds['Sala de Tutorías'],
                'tipo'        => 'personalizada',
                'estado'      => 'en_curso',
                'fecha_inicio'=> $ahora->copy()->subHours(1)->toDateString(),
                'fecha_fin'   => $ahora->copy()->addHours(11)->toDateString(),
                'hora_inicio' => $ahora->copy()->subHours(1)->format('H:i'),
                'hora_fin'    => $ahora->copy()->addHours(11)->format('H:i'),
            ],
            [
                'tipo_actividad_id' => $tipoIds['Investigación'],
                'nombre'      => 'Estudio Grupal: Química',
                'descripcion' => 'Preparación para examen parcial',
                'locacion_id' => $locIds['Sala Principal'],
                'tipo'        => 'personalizada',
                'estado'      => 'en_curso',
                'fecha_inicio'=> $ahora->copy()->subHours(2)->toDateString(),
                'fecha_fin'   => $ahora->copy()->addHours(10)->toDateString(),
                'hora_inicio' => $ahora->copy()->subHours(2)->format('H:i'),
                'hora_fin'    => $ahora->copy()->addHours(10)->format('H:i'),
            ],
            // Finalizada
            [
                'tipo_actividad_id' => $tipoIds['Investigación'],
                'nombre'      => 'Práctica: Circuitos Eléctricos',
                'descripcion' => 'Armado de circuitos en protoboard',
                'locacion_id' => $locIds['Sala de Cómputo'],
                'tipo'        => 'personalizada',
                'estado'      => 'finalizada',
                'fecha_inicio'=> $ahora->copy()->subHours(5)->toDateString(),
                'fecha_fin'   => $ahora->copy()->subHours(5)->toDateString(),
                'hora_inicio' => $ahora->copy()->subHours(5)->format('H:i'),
                'hora_fin'    => $ahora->copy()->subHours(3)->format('H:i'),
            ],
        ];

        foreach ($personalizadas as $act) {
            Actividad::create($act);
        }
    }
}
