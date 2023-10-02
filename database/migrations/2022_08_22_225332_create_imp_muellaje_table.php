<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImpMuellajeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imp_muellaje', function (Blueprint $table) {
            $table->increments('id');

            $table->bigInteger('numRegistroIngreso')->nullable();
            $table->date('fecha');
            $table->text('name');
            $table->text('bandera');
            $table->text('tipo');
            $table->bigInteger('piesEslora');
            $table->text('tipoCarga');
            $table->bigInteger('tonelajeCarga');
            $table->integer('tripulantes');
            $table->integer('pasajeros');
            $table->boolean('sustanciasPeligrosas');
            $table->integer('vehiculos');
            $table->text('claseVehiculo');
            $table->date('fechaPermiso');
            $table->text('titularPermiso');
            $table->bigInteger('numIdent');
            $table->text('nameCap');
            $table->bigInteger('movilCap');
            $table->text('nameCompany');
            $table->bigInteger('movilCompany');
            $table->text('emailCap');
            $table->text('nameNaviera');
            $table->bigInteger('NITNaviera');
            $table->text('nameRep');
            $table->bigInteger('idRep');
            $table->text('dirNotificacion');
            $table->text('municipio');
            $table->text('emailNaviera');
            $table->text('nameRepPago');
            $table->date('fechaAtraque');
            $table->date('fechaSalida');
            $table->bigInteger('tarifa');
            $table->time('horaIngreso');
            $table->time('horaSalida');
            $table->bigInteger('valorDiario');
            $table->integer('numTotalDias');
            $table->bigInteger('valorPago');
            $table->text('observaciones')->nullable();

            //CREADO POR EL FUNCIONARIO
            $table->integer('funcionario_id')->unsigned();
            $table->foreign('funcionario_id')->references('id')->on('users');

            $table->softDeletes();
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
        Schema::dropIfExists('imp_muellaje');
    }
}
