<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoActividadSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tipos_actividad')->insert([
            ['nombre' => 'Otra', 'descripcion' => 'Actividad Nueva', 'activo' => true],
            ['nombre' => 'Académica', 'descripcion' => 'Clase, Tutoria, Conferencia', 'activo' => true],
            ['nombre' => 'Investigación', 'descripcion' => 'Estudio, Estudio Grupal, Revision, Practica', 'activo' => true],
            ['nombre' => 'Recreación', 'descripcion' => 'Lectura, Esparcimiento, Firma', 'activo' => true],
            ['nombre' => 'Administrativa', 'descripcion' => 'Tramite, Pago, Gestion, Prestamo de Libros', 'activo' => true],
            ['nombre' => 'Tecnología', 'descripcion' => 'Uso de Computadores, Uso de Internet', 'activo' => true],
        ]);
    }
}
