<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableNominaEmpleadoDescuento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nomina_empleado_descuentos', function (Blueprint $table) {
            $table->string('tercero_id')->nullable()->after('valor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nomina_empleado_descuentos', function (Blueprint $table) {
            $table->DropColumn('tercero_id');
        });
    }
}
