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
        Schema::create('tareas', function (Blueprint $table) {
            $table->bigIncrements('id');  // Campo id_tarea autoincremental
            $table->unsignedBigInteger('id_usuario'); // Clave foránea
            $table->string('titulo');
            $table->text('descripcion');
            $table->timestamp('fecha_creacion')->useCurrent();  // Establece por defecto el timestamp actual
            $table->timestamp('fecha_limite')->nullable();  // Fecha límite puede ser nulo
            $table->enum('estado', ['pendiente', 'en progreso', 'completada'])->default('pendiente');  // Enum para el estado con valor por defecto 'pendiente'
            $table->timestamps();  // Campos created_at y updated_at

            // Definición de la clave foránea
            $table->foreign('id_usuario')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};
