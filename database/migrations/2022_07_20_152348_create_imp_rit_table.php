<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImpRitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imp_RIT', function (Blueprint $table) {
            $table->increments('id');
            //RELACION CON EL USUARIO
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            //RELACION CON EL FUNCIONARIO QUE VALIDO INSCRIPCIÓN
            $table->integer('user_inscripcion_id')->unsigned()->nullable();
            $table->foreign('user_inscripcion_id')->references('id')->on('users');
            $table->date('fechaInscripUser')->nullable();
            //RELACION CON EL FUNCIONARIO QUE VALIDO ACTUALIZACION
            $table->integer('user_update_id')->unsigned()->nullable();
            $table->foreign('user_update_id')->references('id')->on('users');
            $table->date('fechaUpdateUser')->nullable();
            //RELACION CON EL FUNCIONARIO QUE VALIDO CANCELACIÓN
            $table->integer('user_cancel_id')->unsigned()->nullable();
            $table->foreign('user_cancel_id')->references('id')->on('users');
            $table->date('fechaCancelUser')->nullable();
            //I. ENCABEZADO
            $table->enum('opciondeUso', ['Inscripción', 'Actualización', 'Cancelación']);
            $table->enum('claseContribuyente', ['Retenedor', 'Contribuyente', 'Mixto']);
            $table->text('nameRevFisc')->nullable();
            $table->text('idRevFisc')->nullable();
            $table->text('TPRevFisc')->nullable();
            $table->text('emailRevFisc')->nullable();
            $table->text('movilRevFisc')->nullable();
            $table->text('nameCont')->nullable();
            $table->text('idCont')->nullable();
            $table->text('TPCont')->nullable();
            $table->text('emailCont')->nullable();
            $table->text('movilCont')->nullable();
            //II. DATOS DEL CONTRIBUYENTE o AGENTE RETENEDOR
            $table->enum('tipoDocContri', ['C.C.', 'NIT', 'T.I.','C.E.']);
            $table->text('numDocContri');
            $table->text('DVDocContri');
            $table->enum('natJuridiContri', ['PJ', 'PN', 'SH','PA','CO','UT','CR','SI','SM','UA','DC','LN',
                'EE','EM','EC','EN','ED']);
            $table->enum('tipSociedadContri', ['1', '2', '3','4','5','6','7','8','9','10','11','12']);
            $table->enum('tipEntidadContri', ['20', '21', '22','23']);
            $table->enum('claEntidadContri', ['30', '31', '32','33','34','35','36','37','38','39','40','41',
                '42','43','44','45','46','47','48','49','50','51','52','53','54','55','56','57','58','59','60','61','62']);
            $table->text('apeynomContri');
            $table->boolean('avisos');
            $table->text('dirNotifContri');
            $table->text('barrioContri');
            $table->text('ciudadContri');
            $table->bigInteger('telContri');
            $table->text('webPageContri')->nullable();
            $table->bigInteger('movilContri');
            $table->text('emailContri');
            //III. REPRESENTACIÓN LEGAL
            $table->text('nombreRepLegal');
            $table->text('TDRepLegal');
            $table->bigInteger('IDNumRepLegal');
            $table->text('CRRepLegal');
            $table->text('emailRepLegal');
            $table->bigInteger('telRepLegal');
            $table->text('nombreRepLegal2')->nullable();
            $table->text('TDRepLegal2')->nullable();
            $table->bigInteger('IDNumRepLegal2')->nullable();
            $table->text('CRRepLegal2')->nullable();
            $table->text('emailRepLegal2')->nullable();
            $table->bigInteger('telRepLegal2')->nullable();
            //VI. CANCELACIÓN
            $table->enum('tipoCancelacion', ['Total', 'Parcial'])->nullable();
            $table->enum('motivCancelacion', ['Traspaso', 'Terminación'])->nullable();
            //VII. FIRMAS Y FECHA DE RECEPCIÓN
            $table->date('radicacion');

            //RELACION CON LOS RESOURCES PARA ALMACENAR EL RUT
            $table->integer('rut_resource_id')->unsigned()->nullable();
            $table->foreign('rut_resource_id')->references('id')->on('resources');

            //RELACION CON LOS RESOURCES PARA ALMACENAR CAMARA DE COMERCIO
            $table->integer('cc_resource_id')->unsigned()->nullable();
            $table->foreign('cc_resource_id')->references('id')->on('resources');

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
        Schema::dropIfExists('imp_RIT');
    }
}
