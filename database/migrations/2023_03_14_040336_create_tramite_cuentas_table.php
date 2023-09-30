<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTramiteCuentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tramite_cuentas', function (Blueprint $table) {
            $table->increments('id');
            $table->date('fecha')->nullable();
            $table->string('n_contrato')->nullable();
            $table->integer('v_contrato')->nullable();
            $table->integer('n_pago')->nullable();
            $table->integer('v_pago')->nullable();
            $table->string('proceso')->nullable();
            $table->string('tipo_contrato')->nullable();
            $table->string('otro_tipo_contrato')->nullable();
            $table->string('tipo_pago')->nullable();
            $table->integer('beneficiario_id')->nullable();
            $table->integer('remitente_id')->nullable();
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
        Schema::dropIfExists('tramite_cuentas');
    }
}
