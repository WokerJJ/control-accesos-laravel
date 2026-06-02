<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentoSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('departamento')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('departamento')->insert([
            ['nombre' => 'Amazonas'],
            ['nombre' => 'Antioquia'],
            ['nombre' => 'Arauca'],
            ['nombre' => 'Atlántico'],
            ['nombre' => 'Bogotá D.C.'],
            ['nombre' => 'Bolívar'],
            ['nombre' => 'Boyacá'],
            ['nombre' => 'Caldas'],
            ['nombre' => 'Caquetá'],
            ['nombre' => 'Casanare'],
            ['nombre' => 'Cauca'],
            ['nombre' => 'Cesar'],
            ['nombre' => 'Chocó'],
            ['nombre' => 'Córdoba'],
            ['nombre' => 'Cundinamarca'],
            ['nombre' => 'Guainía'],
            ['nombre' => 'Guaviare'],
            ['nombre' => 'Huila'],
            ['nombre' => 'La Guajira'],
            ['nombre' => 'Magdalena'],
            ['nombre' => 'Meta'],
            ['nombre' => 'Nariño'],
            ['nombre' => 'Norte de Santander'],
            ['nombre' => 'Putumayo'],
            ['nombre' => 'Quindío'],
            ['nombre' => 'Risaralda'],
            ['nombre' => 'San Andrés y Providencia'],
            ['nombre' => 'Santander'],
            ['nombre' => 'Sucre'],
            ['nombre' => 'Tolima'],
            ['nombre' => 'Valle del Cauca'],
            ['nombre' => 'Vaupés'],
            ['nombre' => 'Vichada'],
        ]);
    }
}
