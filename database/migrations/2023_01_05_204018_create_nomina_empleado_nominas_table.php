<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNominaEmpleadoNominasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nomina_empleado_nominas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('nomina_empleado_id');
            $table->integer('dias_laborados');
            $table->integer('horas_extras');
            $table->integer('recargos_nocturnos');
            $table->integer('sueldo');
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
        Schema::dropIfExists('nomina_empleado_nominas');
    }
}
