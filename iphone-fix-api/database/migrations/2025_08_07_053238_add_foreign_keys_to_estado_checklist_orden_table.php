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
        Schema::table('estado_checklist_orden', function (Blueprint $table) {
            $table->foreign(['orden_id'], 'estado_checklist_orden_ibfk_1')->references(['id'])->on('ordenes_servicio');
            $table->foreign(['checklist_id'], 'estado_checklist_orden_ibfk_2')->references(['id'])->on('checklists');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('estado_checklist_orden', function (Blueprint $table) {
            $table->dropForeign('estado_checklist_orden_ibfk_1');
            $table->dropForeign('estado_checklist_orden_ibfk_2');
        });
    }
};
