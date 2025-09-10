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
        Schema::create('detalle_factura', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('factura_id')->index('factura_id');
            $table->unsignedInteger('tipo_item_id')->index('tipo_item_id');
            $table->unsignedInteger('referencia_id');
            $table->text('descripcion')->nullable();
            $table->integer('cantidad')->nullable()->default(1);
            $table->decimal('precio_unitario', 10)->nullable();
            $table->decimal('descuento', 10)->nullable()->default(0);
            $table->decimal('impuesto', 10)->nullable()->default(0);
            $table->decimal('total', 10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_factura');
    }
};
