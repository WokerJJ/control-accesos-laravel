<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_identificacion_id')
                ->constrained('tipo_identificacion')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string('doc_identidad', 20)->unique();
            $table->string('primer_nombre', 50);
            $table->string('segundo_nombre', 50)->nullable();
            $table->string('primer_apellido', 50);
            $table->string('segundo_apellido', 50)->nullable();
            $table->string('email', 100)->nullable()->unique();
            $table->string('celular', 15)->nullable();
            $table->integer('plan')->default(000);
            $table->text('direccion')->nullable();
            $table->foreignId('municipio_id')
                ->nullable()
                ->constrained('municipio')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->date('fecha_registro')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};
