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
        Schema::create('imagenes_orden_servicio', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('orden_id')->index('orden_id');
            $table->string('ruta_imagen');
            $table->enum('tipo', ['ingreso', 'egreso'])->nullable()->default('ingreso');
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('imagenes_orden_servicio');
    }
};
