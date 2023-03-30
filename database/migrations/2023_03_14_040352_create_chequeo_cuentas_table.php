<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChequeoCuentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chequeo_cuentas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tramite_cuenta_id');
            $table->integer('requisito_chequeo_id');
            $table->string('estado')->nullable();
            $table->string('devolucion')->nullable();
            $table->string('observacion')->nullable();
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
        Schema::dropIfExists('chequeo_cuentas');
    }
}
