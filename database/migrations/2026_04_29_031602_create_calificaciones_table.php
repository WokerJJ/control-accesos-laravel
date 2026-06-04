<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('calificaciones')) return;

        Schema::create('calificaciones', function (Blueprint $table) {

            $table->id();

            $table->foreignId('acceso_id')
                ->unique()
                ->constrained('accesos')
                ->cascadeOnDelete();
            $table->tinyInteger('servicio');
            $table->tinyInteger('atencion');
            $table->tinyInteger('lugar');
            $table->tinyInteger('calidad');

            $table->text('comentario')->nullable();

            $table->timestamps();
        });

        // CHECK constraints (MySQL/PostgreSQL only)
        if (config('database.default') !== 'sqlite') {
            DB::statement("
                ALTER TABLE calificaciones
                ADD CONSTRAINT chk_servicio CHECK (servicio BETWEEN 1 AND 5),
                ADD CONSTRAINT chk_atencion CHECK (atencion BETWEEN 1 AND 5),
                ADD CONSTRAINT chk_lugar CHECK (lugar BETWEEN 1 AND 5),
                ADD CONSTRAINT chk_calidad CHECK (calidad BETWEEN 1 AND 5)
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};
