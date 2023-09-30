<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableTesoreriaConciliacionBancaria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tesoreria_conciliacion_bancaria', function (Blueprint $table) {
            $table->integer('partida_sin_conciliacion_libros')->nullable()->after('sumaIgualBank');
            $table->integer('partida_sin_conciliacion_bancos')->nullable()->after('sumaIgualBank');
            $table->boolean('finalizar')->default(FALSE)->after('partida_sin_conciliacion_libros');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tesoreria_conciliacion_bancaria', function (Blueprint $table) {
            $table->dropColumn('partida_sin_conciliacion_libros');
            $table->dropColumn('partida_sin_conciliacion_bancos');
            $table->dropColumn('finalizar');
        });
    }
}
