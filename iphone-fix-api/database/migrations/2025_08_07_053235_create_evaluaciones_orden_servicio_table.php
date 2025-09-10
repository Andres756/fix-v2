<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluaciones_orden_servicio', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('orden_id')->index('orden_id');
            $table->tinyInteger('calificacion')->nullable();
            $table->text('comentario')->nullable();
            $table->timestamp('fecha')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluaciones_orden_servicio');
    }
};
