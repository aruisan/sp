<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImpComunicadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imp_comunicados', function (Blueprint $table) {
            $table->increments('id');

            $table->enum('estado', ['Enviado', 'Visto']);
            $table->dateTime('enviado');
            $table->dateTime('visto')->nullable();
            $table->text('comunicado_title');
            $table->text('comunicado_body');

            $table->integer('destinatario_id')->unsigned();
            $table->foreign('destinatario_id')->references('id')->on('users');
            $table->integer('remitente_id')->unsigned();
            $table->foreign('remitente_id')->references('id')->on('users');

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
        Schema::dropIfExists('imp_comunicados');
    }
}
