<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableNominaEmpleadoNomina extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nomina_empleado_nominas', function (Blueprint $table) {
            $table->integer('prima_antiguedad')->nullable()->after('sueldo');
            $table->integer('bonificacion_recreacion')->nullable()->after('sueldo');
            $table->integer('bonificacion_servicios')->nullable()->after('sueldo');
            $table->integer('bonificacion_direccion')->nullable()->after('sueldo');
            $table->integer('nomina')->after('nomina_empleado_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nomina_empleado_nominas', function (Blueprint $table) {
            $table->DropColumn('prima_antiguedad');
            $table->DropColumn('bonificacion_recreacion');
            $table->DropColumn('bonificacion_servicios');
            $table->DropColumn('bonificacion_direccion');
            $table->DropColumn('nomina');
        });
    }
}
