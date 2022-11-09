<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlmacensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('almacenes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo')->nullable();
            $table->integer('cantidad')->nullable();
            $table->text('nombre_articulo')->nullable();
            $table->string('referencia')->nullable();
            $table->string('ncomin_ingreso')->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->integer('valor_unitario');
            $table->string('ncomin_egreso')->nullable();
            $table->date('fecha_egreso')->nullable();
            $table->integer('dependencia_id');
            $table->integer('owner_id');
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
        Schema::dropIfExists('almacenes');
    }
}
