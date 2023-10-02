<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImpPredialLiquidacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imp_predial_liquidacion', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('año');
            $table->date('fecha_venc');
            $table->bigInteger('avaluo');
            $table->bigInteger('imp_predial');
            $table->bigInteger('tasa_bomberil');
            $table->bigInteger('sub_total');
            $table->bigInteger('int_mora');
            $table->bigInteger('tasa_ambiental');
            $table->bigInteger('int_ambiental');
            $table->bigInteger('tot_año');

            //RELACION CON EL USUARIO
            $table->integer('imp_predial_id')->unsigned();
            $table->foreign('imp_predial_id')->references('id')->on('imp_predial');

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
        Schema::dropIfExists('imp_predial_liquidacion');
    }
}
