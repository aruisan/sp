<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImpPredialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imp_predial', function (Blueprint $table) {
            $table->increments('id');

            $table->bigInteger('cedula');
            $table->text('matricula');
            $table->float('tasaInt');
            $table->float('tarifaMil');
            $table->float('tarifaBomb');
            $table->date('fechaPago');
            $table->float('tasaDesc');
            $table->integer('año');

            //VALORES PAGO IMPUESTO
            $table->bigInteger('tot_imp');
            $table->bigInteger('desc_imp');
            $table->bigInteger('tot_pago');

            //RELACION CON EL USUARIO
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');

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
        Schema::dropIfExists('imp_predial');
    }
}
