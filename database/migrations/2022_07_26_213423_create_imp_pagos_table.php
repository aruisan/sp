<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImpPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imp_pagos', function (Blueprint $table) {
            $table->increments('id');

            $table->enum('modulo', ['ICA-Contribuyente', 'ICA-AgenteRetenedor', 'PREDIAL','MUELLAJE','DELINEACIÓN']);
            $table->integer('entity_id')->unsigned();
            $table->enum('estado', ['Generado', 'Pagado','Borrador']);
            $table->bigInteger('valor');
            $table->date('fechaCreacion');
            $table->date('fechaPago')->nullable();

            //RELACION CON EL DOCUMENTO
            $table->integer('resource_id')->unsigned()->nullable();
            $table->foreign('resource_id')->references('id')->on('resources');

            //RELACION CON EL USUARIO
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            //RELACION CON EL USUARIO
            $table->integer('user_pago_id')->unsigned();
            $table->foreign('user_pago_id')->references('id')->on('users');

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
        Schema::dropIfExists('imp_pagos');
    }
}
