<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run()
    {
        DB::table('roles')->insert([
            ['nombre_rol' => 'Administrador', 'descripcion' => 'Control total', 'estado' => 'activo'],
            ['nombre_rol' => 'Usuario', 'descripcion' => 'Acceso general', 'estado' => 'activo'],
        ]);
    }
}
