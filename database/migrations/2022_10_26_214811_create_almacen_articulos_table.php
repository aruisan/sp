<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlmacenArticulosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('almacen_articulos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('almacen_comprobante_ingreso_id');
            $table->string('codigo')->nullable();
            $table->string('referencia')->nullable();
            $table->integer('cantidad')->nullable();
            $table->text('nombre_articulo')->nullable();
            $table->integer('valor_unitario');
            $table->enum('estado', ['Bueno', 'Regular', 'Malo'])->default('Bueno');
            $table->enum('tipo', ["Devolutivo", "Consumo", "Inmueble Terreno", "Inmueble Edificio"]);
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
        Schema::dropIfExists('almacen_articulos');
    }
}
