<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accesos', function (Blueprint $table) {
            // 1. Date range queries (most common — used by ALL reports, dashboard, exports)
            $table->index('hora_ingreso');

            // 2. Status + date range (dashboard KPIs, "en_curso hoy", "completados")
            $table->index(['estado', 'hora_ingreso']);

            // 3. Activity reports by date range (usadas report, chart queries)
            $table->index(['actividad_id', 'hora_ingreso']);

            // 4. Exit time queries (salidas hoy, duración)
            $table->index('hora_salida');
        });
    }

    public function down(): void
    {
        Schema::table('accesos', function (Blueprint $table) {
            $table->dropIndex(['hora_ingreso']);
            $table->dropIndex(['estado', 'hora_ingreso']);
            $table->dropIndex(['actividad_id', 'hora_ingreso']);
            $table->dropIndex(['hora_salida']);
        });
    }
};
