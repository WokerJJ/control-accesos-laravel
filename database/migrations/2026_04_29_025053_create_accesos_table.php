<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accesos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('personas')->cascadeOnDelete();
            $table->foreignId('locacion_id')->constrained('locacion');
            $table->foreignId('actividad_id')->constrained('actividades')->restrictOnDelete();
            $table->foreignId('casillero_id')->constrained('casilleros');
            $table->dateTime('hora_ingreso')->useCurrent();
            $table->dateTime('hora_salida')->nullable();
            $table->integer('duracion')->nullable();
            $table->enum('metodo_acceso', ['tarjeta', 'codigo', 'manual'])->default('manual');
            $table->enum('estado', ['en_curso', 'completado', 'cancelado'])->default('en_curso');
            $table->timestamps();
            $table->index(['persona_id', 'hora_ingreso']);
            $table->index(['locacion_id', 'hora_ingreso']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accesos');
    }
};
