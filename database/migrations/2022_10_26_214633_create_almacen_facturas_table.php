<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlmacenFacturasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('almacen_facturas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('numero_factura')->nullable();
            $table->string('comprobante_ingreso')->nullable();
            $table->string('comprobante_egreso')->nullable();
            $table->date('ff_ingreso')->nullable();
            $table->date('ff_egreso')->nullable();
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
        Schema::dropIfExists('almacen_facturas');
    }
}
