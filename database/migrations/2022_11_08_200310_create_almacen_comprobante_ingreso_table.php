<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlmacenComprobanteIngresoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('almacen_comprobante_ingresos', function (Blueprint $table) {
            $table->increments('id');
            $table->date('fecha');
            $table->string('contrato')->nullable();
            $table->string('fecha_contrato')->nullable();
            $table->string('factura')->nullable();
            $table->string('fecha_factura')->nullable();
            $table->string('ccd')->nullable();
            $table->string('ccc')->nullable();
            $table->integer('owner_id');
            $table->integer('proovedor_id');
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
        Schema::dropIfExists('almacen_comprobante_ingresos');
    }
}
