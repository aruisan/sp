<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAprobadorCuentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aprobador_cuentas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('aprobado_user_id');
            $table->integer('tramite_cuenta_id');
            $table->string('estado');
            $table->datetime('recibido')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aprobador_cuentas');
    }
}
