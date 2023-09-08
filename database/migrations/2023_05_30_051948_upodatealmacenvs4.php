<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Upodatealmacenvs4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('almacen_articulo_salidas', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('observacion');
        });

        Schema::table('almacen_comprobante_egresos', function (Blueprint $table) {
            $table->json('status')->after('fecha');
            $table->json('observacion')->after('fecha');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('almacen_articulo_salidas', function (Blueprint $table) {
            $table->json('status')->after('id');
            $table->json('observacion')->after('id');
        });
        
        Schema::table('almacen_comprobante_egresos', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('observacion');
        });
    }
}
