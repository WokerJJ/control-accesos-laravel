<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocacionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('locacion')->insert([
            ['nombre' => 'Sala Principal'],
            ['nombre' => 'Sala de Cómputo'],
            ['nombre' => 'Sala de Lectura'],
            ['nombre' => 'Auditorio'],
            ['nombre' => 'Sala de Tutorías'],
            ['nombre' => 'Zona de Préstamo'],
            ['nombre' => 'Cubículo Profesores'],
        ]);
    }
}
