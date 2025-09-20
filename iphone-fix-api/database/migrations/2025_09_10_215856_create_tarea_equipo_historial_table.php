<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tarea_equipo_historial', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tarea_equipo_id'); // FK a tareas_equipo
            $table->unsignedBigInteger('tecnico_id')->nullable(); // técnico que hizo el cambio
            $table->string('estado_anterior')->nullable();
            $table->string('estado_nuevo');
            $table->timestamp('cambiado_en')->useCurrent();
            $table->timestamps();

            // Llaves foráneas
            $table->foreign('tarea_equipo_id')
                ->references('id')
                ->on('tareas_equipo')
                ->onDelete('cascade');

            $table->foreign('tecnico_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarea_equipo_historial');
    }
};
