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
        Schema::create('estado_checklist_orden', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('orden_id')->index('orden_id');
            $table->unsignedInteger('checklist_id')->index('checklist_id');
            $table->enum('estado_inicial', ['bueno', 'regular', 'malo'])->nullable();
            $table->enum('estado_final', ['bueno', 'regular', 'malo'])->nullable();
            $table->text('observaciones')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estado_checklist_orden');
    }
};
