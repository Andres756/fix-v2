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
        Schema::create('ordenes_servicio', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo_orden', 50)->nullable()->unique('codigo_orden');
            $table->unsignedInteger('cliente_id')->index('cliente_id');
            $table->unsignedInteger('equipo_id')->index('equipo_id');
            $table->unsignedBigInteger('tecnico_id')->nullable()->index('tecnico_id');
            $table->unsignedInteger('estado_id')->index('estado_id');
            $table->text('notas_cliente')->nullable();
            $table->text('notas_internas')->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_entrega')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordenes_servicio');
    }
};
