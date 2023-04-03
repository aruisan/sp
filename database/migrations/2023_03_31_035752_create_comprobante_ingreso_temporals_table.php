<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComprobanteIngresoTemporalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        Schema::create('comprobante_ingreso_temporales', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('fecha');
            $table->string('referencia');
            $table->string('cc');
            $table->string('tercero');
            $table->integer('valor');
            $table->text('concepto');
            $table->boolean('check')->default(0);
            $table->timestamps();
        });
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comprobante_ingreso_temporales');
    }
}
