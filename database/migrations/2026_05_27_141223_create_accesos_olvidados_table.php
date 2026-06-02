<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accesos_olvidados', function (Blueprint $table) {

            $table->id();

            $table->foreignId('acceso_id')
                ->constrained('accesos')
                ->cascadeOnDelete();
            $table->timestamp('hora_cierre_forzado');
            $table->string('motivo')
                ->default('cierre_diario');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accesos_olvidados');
    }
};
