<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('actividades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_actividad_id')
                ->constrained('tipos_actividad');

            // Datos básicos
            $table->string('nombre');
            $table->text('descripcion')->nullable();

            // Tipo de programación temporal
            $table->enum('tipo', ['fija', 'programada', 'personalizada']);

            // Estado de disponibilidad del servicio
            $table->enum('estado', ['en_curso', 'pendiente', 'finalizada', 'cancelada'])->default('en_curso');

            // Fechas y horas (para programadas y personalizadas)
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();

            // Relación con locación
            $table->foreignId('locacion_id')
                ->constrained('locacion');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
