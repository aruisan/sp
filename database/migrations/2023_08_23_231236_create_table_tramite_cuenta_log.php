<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTramiteCuentaLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tramites_cuentas_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tramite_cuenta_id');
            $table->string('accion');
            $table->string('rol');
            $table->text('observacion')->nullable();
            $table->date('fecha');   
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tramites_cuentas_logs');
    }
}
