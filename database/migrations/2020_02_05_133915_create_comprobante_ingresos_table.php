<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComprobanteIngresosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comprobante_ingresos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('code');
            $table->text('concepto');
            $table->integer('val_total');
            $table->integer('valor');
            $table->integer('iva')->nullable()->default(0);
            $table->enum('estado', [0, 1, 2, 3]);
            $table->date('ff');
            $table->string('ruta')->nullable();
            $table->enum('tipoCI', ['SGP Salud', 'SGP Educacion', 'SGP Otros sectores', 'Otro'])->nullable();
            $table->text('cualOtroTipo')->nullable();

            $table->integer('vigencia_id')->unsigned();
            $table->foreign('vigencia_id')->references('id')->on('vigencias');
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
        Schema::dropIfExists('comprobante_ingresos');
    }
}
