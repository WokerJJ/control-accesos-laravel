<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CasilleroSeeder extends Seeder
{
    public function run(): void
    {
        // Casillero externo (Null Object) — ID fijo 1, siempre disponible
        DB::table('casilleros')->insert([
            'id'     => 1,
            'codigo' => 'EXT-00',
            'estado' => 'libre',
        ]);

        // Casilleros normales generados dinámicamente
        $filas   = ['A', 'B'];
        $columnas = range(1, 10);

        DB::table('casilleros')->insert(
            collect($filas)
                ->crossJoin($columnas)
                ->map(fn($par) => ['codigo' => $par[0] . $par[1], 'estado' => 'libre'])
                ->all()
        );
    }
}
