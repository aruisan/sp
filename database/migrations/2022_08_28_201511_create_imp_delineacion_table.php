<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImpDelineacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imp_delineacion', function (Blueprint $table) {
            $table->increments('id');

            $table->enum('tramite', ['INICIAL', 'MODIFICACIÓN', 'REVALIDACIÓN']);
            $table->enum('tipoTramite', ['LICENCIA DE URBANIZACIÓN', 'LICENCIA DE PARCELACIÓN', 'LICENCIA DE SUBDIVISIÓN',
                'LICENCIA DE CONSTRUCCIÓN','INTERVENCIÓN Y OCUPACIÓN DEL ESPACIO PÚBLICO','RECONOCIMIENTO DE LA EXISTENCIA DE UNA EDIFICACIÓN',
                'OTRAS ACTUACIONES']);
            $table->text('cualOtraActuacion')->nullable();
            $table->enum('modalidadLicenciaUrbanizacion', ['DESARROLLO', 'SANEAMIENTO', 'REURBANIZACIÓN']);
            $table->enum('modalidadLicenciaSubdivision', ['SUBDIVISIÓN RURAL', 'SUBDIVISIÓN URBANA', 'RELOTEO']);
            $table->enum('modalidadLicenciaConstruccion', ['OBRA NUEVA', 'AMPLIACIÓN', 'ADECUACIÓN','MODIFICACIÓN',
                'RESTAURACIÓN','REFORZAMIENTO ESTRUCTURAL','DEMOLICIÓN TOTAL','DEMOLICIÓN PARCIAL','RECONSTRUCCIÓN','CERRAMIENTO']);
            $table->enum('usos', ['Vivienda', 'Comercio y/o servicios', 'Institucional/Dotacional','Industrial', 'Otro']);
            $table->text('cualOtroUso')->nullable();
            $table->enum('area', ['Menor a 2.000 m2', 'Igual o mayor a 2.000 m2', 'Alcanza o supera mediante ampliaciones los 2.000 m2','Genera 5 o más unidades de vivienda para transferir a terceros']);
            $table->enum('tipoVivienda', ['VIP', 'VIS', 'No VIS']);
            $table->enum('interesCultural', ['No', 'Si']);
            $table->text('dirActual');
            $table->text('dirAnterior');
            $table->bigInteger('matricula');
            $table->bigInteger('idCatastral');
            $table->enum('clasificacionSuelo', ['URBANO', 'RURAL', 'DE EXPANSIÓN']);
            $table->enum('planimetria', ['Plano del Loteo', 'Plano Topográfico', 'Otro']);
            $table->text('cualotraPlanimetria')->nullable();
            $table->text('barrio');
            $table->text('vereda')->nullable();
            $table->text('comuna')->nullable();
            $table->text('sector')->nullable();
            $table->integer('estrato');
            $table->text('corregimiento')->nullable();
            $table->integer('manzana')->nullable();
            $table->integer('lote')->nullable();
            $table->bigInteger('longNorte1')->nullable();
            $table->text('colindNorte1')->nullable();
            $table->text('linderosNorte2')->nullable();
            $table->bigInteger('longNorte2')->nullable();
            $table->text('colindNorte2')->nullable();
            $table->text('linderosNorte3')->nullable();
            $table->bigInteger('longNorte3')->nullable();
            $table->text('colindNorte3')->nullable();
            $table->text('linderosNorte4')->nullable();
            $table->bigInteger('longNorte4')->nullable();
            $table->text('colindNorte4')->nullable();
            $table->bigInteger('longSur1')->nullable();
            $table->text('colindSur1')->nullable();
            $table->text('linderosSur2')->nullable();
            $table->bigInteger('longSur2')->nullable();
            $table->text('colindSur2')->nullable();
            $table->text('linderosSur3')->nullable();
            $table->bigInteger('longSur3')->nullable();
            $table->text('colindSur3')->nullable();
            $table->text('linderosSur4')->nullable();
            $table->bigInteger('longSur4')->nullable();
            $table->text('colindSur4')->nullable();
            $table->bigInteger('longOriente1')->nullable();
            $table->text('colindOriente1')->nullable();
            $table->text('linderosOriente2')->nullable();
            $table->bigInteger('longOriente2')->nullable();
            $table->text('colindOriente2')->nullable();
            $table->text('linderosOriente3')->nullable();
            $table->bigInteger('longOriente3')->nullable();
            $table->text('colindOriente3')->nullable();
            $table->text('linderosOriente4')->nullable();
            $table->bigInteger('longOriente4')->nullable();
            $table->text('colindOriente4')->nullable();
            $table->bigInteger('longOccidente1')->nullable();
            $table->text('colindOccidente1')->nullable();
            $table->text('linderosOccidente2')->nullable();
            $table->bigInteger('longOccidente2')->nullable();
            $table->text('colindOccidente2')->nullable();
            $table->text('linderosOccidente3')->nullable();
            $table->bigInteger('longOccidente3')->nullable();
            $table->text('colindOccidente3')->nullable();
            $table->text('linderosOccidente4')->nullable();
            $table->bigInteger('longOccidente4')->nullable();
            $table->text('colindOccidente4')->nullable();
            $table->integer('areaTotalPredios');
            $table->enum('notificar', ['SI', 'NO']);
            $table->text('nameUrbanizador')->nullable();
            $table->bigInteger('ccUrbanizador')->nullable();
            $table->bigInteger('numMatUrbanizador')->nullable();
            $table->date('fechaExpMatUrbanizador')->nullable();
            $table->text('emailUrbanizador')->nullable();
            $table->bigInteger('telUrbanizador')->nullable();
            $table->text('nameDir')->nullable();
            $table->bigInteger('ccDir')->nullable();
            $table->bigInteger('numMatDir')->nullable();
            $table->date('fechaExpMatDir')->nullable();
            $table->text('emailDir')->nullable();
            $table->bigInteger('telDir')->nullable();
            $table->text('nameArq')->nullable();
            $table->bigInteger('ccArq')->nullable();
            $table->bigInteger('numMatArq')->nullable();
            $table->date('fechaExpMatArq')->nullable();
            $table->text('emailArq')->nullable();
            $table->bigInteger('telArq')->nullable();
            $table->text('nameIngCivilDis')->nullable();
            $table->bigInteger('ccIngCivilDis')->nullable();
            $table->bigInteger('numMatIngCivilDis')->nullable();
            $table->date('fechaExpMatIngCivilDis')->nullable();
            $table->text('emailIngCivilDis')->nullable();
            $table->bigInteger('telIngCivilDis')->nullable();
            $table->enum('supervTecnicaIngCivilDis', ['Si', 'No']);
            $table->text('nameDiseñadorElem')->nullable();
            $table->bigInteger('ccDiseñadorElem')->nullable();
            $table->bigInteger('numMatDiseñadorElem')->nullable();
            $table->date('fechaExpMatDiseñadorElem')->nullable();
            $table->text('emailDiseñadorElem')->nullable();
            $table->bigInteger('telDiseñadorElem')->nullable();
            $table->text('nameIngCivilGeo')->nullable();
            $table->bigInteger('ccIngCivilGeo')->nullable();
            $table->bigInteger('numMatIngCivilGeo')->nullable();
            $table->date('fechaExpMatIngCivilGeo')->nullable();
            $table->text('emailIngCivilGeo')->nullable();
            $table->bigInteger('telIngCivilGeo')->nullable();
            $table->enum('supervTecnicaIngCivilGeo', ['Si', 'No']);
            $table->text('nameTopografo')->nullable();
            $table->bigInteger('ccTopografo')->nullable();
            $table->bigInteger('numMatTopografo')->nullable();
            $table->date('fechaExpMatTopografo')->nullable();
            $table->text('emailTopografo')->nullable();
            $table->bigInteger('telTopografo')->nullable();
            $table->text('nameRevisor')->nullable();
            $table->bigInteger('ccRevisor')->nullable();
            $table->bigInteger('numMatRevisor')->nullable();
            $table->date('fechaExpMatRevisor')->nullable();
            $table->text('emailRevisor')->nullable();
            $table->bigInteger('telRevisor')->nullable();
            $table->text('nameOtroProf1')->nullable();
            $table->bigInteger('ccOtroProf1')->nullable();
            $table->bigInteger('numMatOtroProf1')->nullable();
            $table->date('fechaExpMatOtroProf1')->nullable();
            $table->text('emailOtroProf1')->nullable();
            $table->bigInteger('telOtroProf1')->nullable();
            $table->text('nameOtroProf2')->nullable();
            $table->bigInteger('ccOtroProf2')->nullable();
            $table->bigInteger('numMatOtroProf2')->nullable();
            $table->date('fechaExpMatOtroProf2')->nullable();
            $table->text('emailOtroProf2')->nullable();
            $table->bigInteger('telOtroProf2')->nullable();
            $table->text('nameResponsable')->nullable();
            $table->bigInteger('ccResponsable')->nullable();
            $table->text('dirResponsable')->nullable();
            $table->text('emailResponsable')->nullable();
            $table->bigInteger('telResponsable')->nullable();
            $table->enum('notificarResponsable', ['SI', 'NO']);
            $table->enum('tipoUsos', ['Vivienda', 'Institucional/Dotacional', 'Educativo', 'Salud','Industrial','Comercio/Servicios','Otro']);
            $table->text('cualotroTipoUso')->nullable();
            $table->enum('medidasPasivas', ['Cubierta verde', 'Elementos de protección solar', 'Vidrios de protección solar',
                'Cubierta de protección solar','Pared de protección solar','Otro']);
            $table->text('cualotraMedidaPasiva')->nullable();
            $table->enum('medidasActivas', ['Iluminación eficiente', 'Equipos de aire acondicionado eficientes', 'Agua caliente solar',
                'Controles de iluminación','Variadores de velocidad para bombas','Otro']);
            $table->text('cualotraMedidaActiva')->nullable();
            $table->enum('materialidadMuroExt', ['Ladrillo portante', 'Ladrillo común', 'Muro de concreto vaciado en obra', 'Muro en superboard','Muro cortina en aluminio','Otro']);
            $table->text('cualotroMuroExt')->nullable();
            $table->enum('materialidadMuroInt', ['Ladrillo número 4 o similar', 'Drywall', 'Ladrillo común', 'Muro de concreto vaciado en obra','Mamposteria de bloque de concreto','Otro']);
            $table->text('cualotroMuroInt')->nullable();
            $table->enum('materialidadCubierta', ['Cubierta de concreto vaciado en obra', 'Panel tipo sándwich de aluminio', 'Tejas de arcilla', 'Metálica','Fibrocemento','Otro']);
            $table->text('cualotroCub')->nullable();
            $table->integer('relacionNorte')->nullable();
            $table->integer('relacionSur')->nullable();
            $table->integer('relacionOriente')->nullable();
            $table->integer('relacionOccidente')->nullable();
            $table->integer('relacionAltura')->nullable();
            $table->enum('declaracionMedidasAhorroAgua', ['Sanitarios de bajo consumo', 'Lavamanos de bajo consumo', 'Duchas de bajo consumo',
                'Orinales de bajo consumo','Recolección de agua lluvia','Otro']);
            $table->text('cualotroAhorroAgua')->nullable();
            $table->enum('zonificacionClimatica', ['Frío', 'Templado', 'Cálido seco', 'Cálido húmedo']);
            $table->enum('zonaClimatica', ['Sí', 'No', 'Otro']);
            $table->text('cualotroZonificacionClimatica')->nullable();
            $table->text('ahorroEsperadoAgua')->nullable();
            $table->text('ahorroEsperadoEnergia')->nullable();
            $table->bigInteger('urbanismoPaisajismo')->nullable();
            $table->bigInteger('zonasComunes')->nullable();
            $table->bigInteger('parqueaderos')->nullable();

            $table->date('fecha');

            //CREADO POR EL FUNCIONARIO
            $table->integer('funcionario_id')->unsigned();
            $table->foreign('funcionario_id')->references('id')->on('users');

            $table->softDeletes();
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
        Schema::dropIfExists('imp_delineacion');
    }
}
