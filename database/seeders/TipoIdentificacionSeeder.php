<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoIdentificacionSeeder extends Seeder
{
    public function run()
    {
        DB::table('tipo_identificacion')->insert([
            ['abreviatura' => 'CC', 'descripcion' => 'Cédula de Ciudadanía'],
            ['abreviatura' => 'TI', 'descripcion' => 'Tarjeta de Identidad'],
            ['abreviatura' => 'CE', 'descripcion' => 'Cédula de Extranjería'],
        ]);
    }
}
