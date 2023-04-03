<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldTableComprobanteIngresoTemporal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        Schema::table('comprobante_ingreso_temporales', function (Blueprint $table) {
            $table->integer('conciliacion_id')->nullable()->after('concepto');
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
        Schema::table('comprobante_ingreso_temporales', function (Blueprint $table) {
            $table->dropColumn('conciliacion_id');
        });
    }
}
