<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run()
    {
        $this->call([
            TipoIdentificacionSeeder::class,
            RolesSeeder::class,
            DepartamentoSeeder::class,
            MunicipioSeeder::class,
            LocacionSeeder::class,
            TipoActividadSeeder::class,
            ActividadesSeeder::class,
            UsuarioSeeder::class,
            CasilleroSeeder::class,
            AccesoSeeder::class,
        ]);
    }
}
