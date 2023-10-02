<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComprobanteIngresoTemporalConciliacionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comprobante_ingreso_temporal_conciliaciones', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('conciliacion_id');
            $table->integer('comprobante_ingreso_temporal_id');
            $table->boolean('check')->default(0);
            $table->timestamps();
        });

        Schema::table('comprobante_ingreso_temporales', function (Blueprint $table) {
            $table->dropColumn('conciliacion_id');
            $table->dropColumn('check');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comprobante_ingreso_temporal_conciliaciones');

        Schema::table('comprobante_ingreso_temporales', function (Blueprint $table) {
            $table->integer('conciliacion_id')->after('concepto');
            $table->boolean('check')->default(0)->after('concepto');
        });
    }
}
