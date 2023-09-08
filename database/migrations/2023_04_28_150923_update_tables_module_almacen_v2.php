<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTablesModuleAlmacenV2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('almacen_articulo_salidas', function (Blueprint $table) {
            $table->json('status')->after('id');
        });

        Schema::table('dependencias', function (Blueprint $table) {
            $table->integer('encargado_id')->nullable()->after('id');
        });

        Schema::table('almacen_comprobante_egresos', function (Blueprint $table) {
            $table->text('ccc')->after('id');
            $table->text('ccd')->after('id');
        });
        
        Schema::table('almacen_articulos', function (Blueprint $table) {
            $table->integer('ccc')->after('id');
            $table->integer('ccd')->after('id');
        });
        
        Schema::table('almacen_comprobante_ingresos', function (Blueprint $table) {
            $table->dropColumn('ccc');
            $table->dropColumn('ccd');
        });
        /*
        */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('almacen_articulo_salidas', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('dependencias', function (Blueprint $table) {
            $table->dropColumn('encargado_id');
        });
        Schema::table('almacen_comprobante_egresos', function (Blueprint $table) {
            $table->dropColumn('ccc');
            $table->dropColumn('ccd');
        });
        Schema::table('almacen_articulos', function (Blueprint $table) {
            $table->dropColumn('ccc');
            $table->dropColumn('ccd');
        });
        
        Schema::table('almacen_comprobante_ingresos', function (Blueprint $table) {
            $table->integer('ccc')->after('id');
            $table->integer('ccd')->after('id');
        });
        /*
        */
    }
}
