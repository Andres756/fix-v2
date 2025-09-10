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
        Schema::create('historial_estados_orden', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('orden_id')->index('orden_id');
            $table->unsignedInteger('estado_anterior_id')->nullable()->index('estado_anterior_id');
            $table->unsignedInteger('estado_nuevo_id')->index('estado_nuevo_id');
            $table->unsignedBigInteger('user_id')->nullable()->index('user_id');
            $table->text('comentario')->nullable();
            $table->timestamp('fecha_cambio')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historial_estados_orden');
    }
};
