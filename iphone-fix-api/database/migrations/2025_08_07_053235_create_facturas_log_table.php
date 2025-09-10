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
        Schema::create('facturas_log', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('factura_id')->index('factura_id');
            $table->unsignedInteger('accion_id')->index('accion_id');
            $table->unsignedBigInteger('usuario_id')->nullable()->index('usuario_id');
            $table->text('detalle')->nullable();
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
        Schema::dropIfExists('facturas_log');
    }
};
