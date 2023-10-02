<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->integer('carpeta_id')->after('tercero_id');
            $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->enum('type', [
                'Actas','Acuerdos','Resoluciones','Proyectos de acuerdo','Lista de empleados','Manual de contratación','Plan de adquisiones','Procesos de Contratación','Correspondencia entrada','Correspondencia salida','Otros documentos'
            ])->after('tercero_id');
            $table->dropColumn('carpeta_id');
        });
    }
}
