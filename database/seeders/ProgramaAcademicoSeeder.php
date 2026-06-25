<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramaAcademicoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('programas_academicos')->insert([
            // ── Roles especiales ──
            [
                'codigo'   => '0001',
                'nombre'   => 'Profesor',
                'tipo'     => 'profesor',
                'estado'   => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo'   => '0002',
                'nombre'   => 'Administrativo',
                'tipo'     => 'administrativo',
                'estado'   => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo'   => '0003',
                'nombre'   => 'Externo',
                'tipo'     => 'externo',
                'estado'   => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ── Carreras ──
            [
                'codigo'   => '1001',
                'nombre'   => 'Ingeniería de Sistemas',
                'tipo'     => 'carrera',
                'estado'   => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo'   => '1002',
                'nombre'   => 'Ingeniería Electrónica',
                'tipo'     => 'carrera',
                'estado'   => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo'   => '2001',
                'nombre'   => 'Administración de Empresas',
                'tipo'     => 'carrera',
                'estado'   => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo'   => '2002',
                'nombre'   => 'Contaduría Pública',
                'tipo'     => 'carrera',
                'estado'   => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo'   => '3001',
                'nombre'   => 'Derecho',
                'tipo'     => 'carrera',
                'estado'   => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo'   => '3002',
                'nombre'   => 'Medicina',
                'tipo'     => 'carrera',
                'estado'   => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo'   => '4001',
                'nombre'   => 'Psicología',
                'tipo'     => 'carrera',
                'estado'   => 'activo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
