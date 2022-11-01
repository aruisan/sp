<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\NominaEmpleado;

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
            $table->string('nombre');
            $table->string('email');
            $table->string('direccion');
            $table->string('fecha_nacimiento');
            $table->string('telefono');
            $table->string('cargo');
            $table->integer('codigo_cargo');
            $table->enum('tipo_cargo', [
                NominaEmpleado::TIPO_CARGO_1,
                NominaEmpleado::TIPO_CARGO_2,
                NominaEmpleado::TIPO_CARGO_3,
                NominaEmpleado::TIPO_CARGO_4
            ]);
            $table->string('grado');
            $table->integer('apto_administrativo_numero');
            $table->string('apto_administrativo_fecha');
            $table->string('apto_administrativo_archivo');
            $table->string('eps');
            $table->string('fondo_pensiones');
            $table->enum('tipo_cuenta_bancaria', [
                NominaEmpleado::TIPO_CUENTA_BANCARIA_1,
                NominaEmpleado::TIPO_CUENTA_BANCARIA_2
            ]);
            $table->integer('numero_cuenta_bancaria');
            $table->string('banco_cuenta_bancaria');
            $table->string('certificado_cuenta_bancaria');
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
