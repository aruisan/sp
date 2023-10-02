<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpgradeModuleNominaDescuentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nomina_empleado_descuentos', function (Blueprint $table) {
            $table->integer('padre_id')->nullable();
            $table->integer('n_cuotas')->default(1);
            $table->bigInteger('valor_total')->nullable();
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
            $table->dropColumn('padre_id');
            $table->dropColumn('n_cuotas');
            $table->dropColumn('valor_total');
        });
    }
}
