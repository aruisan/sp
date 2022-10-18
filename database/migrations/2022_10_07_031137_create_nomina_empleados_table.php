<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNominaEmpleadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
    */
    public function up()
    {
        Schema::create('nomina_empleados', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('num_dc');
            $table->string('email');
            $table->string('direccion');
            $table->string('fecha_nacimiento');
            $table->string('telefono');
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
        Schema::dropIfExists('nomina_empleados');
    }
}
