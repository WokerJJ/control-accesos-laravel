<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [];
        $personas = [];

        $externoId = DB::table('programas_academicos')->where('codigo', '0003')->value('id');

        for ($i = 1; $i <= 200; $i++) {

            $personas[] = [
                'tipo_identificacion_id' => 1,
                'doc_identidad' => str_pad((string)(1007549900 + $i), 10, '0', STR_PAD_LEFT),
                'primer_nombre' => 'User' . $i,
                'primer_apellido' => 'Test',
                'email' => "user{$i}@mail.com",
                'programa_academico_id' => $externoId,
                'codigo_institucional' => 'EXTERNO',
                'estado' => 'activo',
                'fecha_registro' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('personas')->insert($personas);

        $ids = DB::table('personas')->pluck('id');

        $password = Hash::make('123456');
        foreach ($ids as $id) {
            $usuarios[] = [
                'persona_id' => $id,
                'rol_id' => 1,
                'password_hash' => $password,
                'estado' => 'activo',
                'ultimo_acceso' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('usuarios')->insert($usuarios);
    }
}
