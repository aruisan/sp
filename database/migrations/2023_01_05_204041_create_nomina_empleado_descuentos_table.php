<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNominaEmpleadoDescuentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nomina_empleado_descuentos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->integer('valor');
            $table->integer('nomina_empleado_nomina_id');
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
        Schema::dropIfExists('nomina_empleado_descuentos');
    }
}
