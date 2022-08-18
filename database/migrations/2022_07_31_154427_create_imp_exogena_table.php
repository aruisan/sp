<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImpExogenaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imp_exogena', function (Blueprint $table) {
            $table->increments('id');

            //RELACION CON EL USUARIO
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

            $table->text('año');
            $table->text('numIdeInform');
            $table->text('dv');
            $table->text('primerApe');
            $table->text('segApe');
            $table->text('primerNom');
            $table->text('otrosNombres')->nullable();
            $table->text('razonSocial');
            $table->text('dir');
            $table->text('tel');
            $table->text('email');
            //RELACION CON EL DEPARTAMENTO
            $table->integer('codeDpto')->unsigned();
            //RELACION CON EL DEPARTAMENTO
            $table->integer('codeCiudad')->unsigned();
            $table->foreign('codeCiudad')->references('id')->on('imp_code_muni');
            //RELACION CON CIUU
            $table->integer('ciuu_id')->unsigned();
            $table->foreign('ciuu_id')->references('id')->on('imp_ciuu');

            $table->integer('valorAcum');
            $table->integer('tarifa');
            $table->integer('valorReten');
            $table->integer('valorRetenAsum');

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
        Schema::dropIfExists('imp_exogena');
    }
}
