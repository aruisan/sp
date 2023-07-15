<?php

Route::get('/', 'VisitanteController@index');

Route::get('/info', function(){
    dd(phpinfo());
});

Auth::routes();

//Route::get('/home', 'Cobro\HomeController@index')->name('home');

Route::group([ 'middleware' => 'auth'] ,function(){
    Route::get('bbdd_backup', 'Admin\ConfigGeneralController@bbdd_backup')->name('bbdd_backup');
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');

	Route::resource('predios', 'Cobro\PredioController');
    Route::get('predios-sin-asignar', 'Cobro\PredioController@predioSinAsignar')->name('unnassigned');
    Route::get('predios-asignados', 'Cobro\PredioController@predioAsignado')->name('assignor');
    Route::post('predios-asignar', 'Cobro\PredioController@predioAsignarAdministrativeStore')->name('assignor.store');
    Route::get('predio-expediente/{id}', 'PredioController@asignarExpediente')->name('assignor.expedient');
    Route::get('predio-detail/{id}', 'Cobro\PredioController@show')->name('predio.detail');
  
    Route::post('predio-asignar', 'Cobro\PersonaPredioController@predioAsignarPersona');
    Route::post('importar', 'Cobro\ImportController@import')->name('importar.predios');

    Route::get('asignar/{id}', 'Cobro\AsignarController@index');
    Route::resource('asignar', 'Cobro\AsignarController');

	//gestion de creacion y relacion de personas
	Route::post('persona/relacionar', 'PersonasController@PersonafindCreate')->name('persona.relacionar');
	Route::get('persona-identtificar/{identificador}', 'PersonasController@personaIdentificar')->name('persona.identificar');
	Route::post('avatar', 'UserController@editAvatar')->name('user-avatar');
	Route::post('password', 'UserController@editPassword')->name('user-password');

    Route::resource('personas', 'PersonasController');
    Route::get('persona-find/{identificador}', 'PersonasController@personaFind');
    Route::post('persona/find-create', 'PersonasController@PersonafindCreate');
    Route::resource('personas-predios', 'Cobro\PersonaPredioController');
	//Route::get('usuarios-tipo/{id}', 'UserController@userstype');

    ////////////////////IMPUESTOS//////////////////
    Route::group(['prefix' => 'impuestos'] ,function ()
    {
        Route::resource('/', 'Impuestos\ImpuestosController');

        //DESCARGAR DOCUMENTACION DE AYUDA
        Route::get('/download/{file}' , 'Impuestos\ImpuestosController@downloadFile');

        //COMUNICADOS
        Route::resource('/comunicados', 'Impuestos\Comunicados\ComunicadosController');
        Route::post('/comunicados/message', 'Impuestos\Comunicados\ComunicadosController@getMessage');

        //RIT
        Route::get('/RIT/create', 'Impuestos\RIT\RitController@create')->name('impuestos.rit.create');
        Route::get('/RIT/update', 'Impuestos\RIT\RitController@updateRIT')->name('impuestos.rit.update');
        Route::get('/RIT/restore', 'Impuestos\RIT\RitController@restoreRIT')->name('impuestos.rit.restore');
        Route::get('/RIT/{id}', 'Impuestos\ImpuestosController@pdfRIT')->name('impuestos.rit.pdf');
        Route::post('/RIT', 'Impuestos\RIT\RitController@store')->name('impuestos.rit.store');
        Route::delete('/RIT/actividad/delete/{id}', 'Impuestos\RIT\RitController@deleteActividad');
        Route::delete('/RIT/establecimiento/delete/{id}', 'Impuestos\RIT\RitController@deleteEstablecimiento');

        //ICA
            //CONTRIBUYENTE
        Route::get('/ICA/contri/create','Impuestos\ICA\IcaController@createContri')->name('impuestos.icaContri.create');
        Route::get('/ICA/contri/update/{id}','Impuestos\ICA\IcaController@updateContri')->name('impuestos.icaContri.update');
        Route::post('/ICA/contri', 'Impuestos\ICA\IcaController@storeContri')->name('impuestos.icaContri.store');
        Route::get('/ICA/contri/pdf/{id}', 'Impuestos\ICA\IcaController@facturaContri')->name('impuestos.icaContri.factura');
        Route::get('/ICA/contri/form/{id}', 'Impuestos\ICA\IcaController@formContri')->name('impuestos.icaContri.formulario');
        Route::delete('/ICA/contri/delete/{idForm}/{idPay}', 'Impuestos\ICA\IcaController@deleteContri');

            //AGENTE RETENEDOR
        Route::get('/ICA/retenedor/create','Impuestos\ICA\IcaController@createRetenedor')->name('impuestos.icaRetenedor.create');
        Route::get('/ICA/retenedor/update/{id}','Impuestos\ICA\IcaController@updateRetenedor')->name('impuestos.icaRetenedor.update');
        Route::post('/ICA/retenedor', 'Impuestos\ICA\IcaController@storeRetenedor')->name('impuestos.icaRetenedor.store');
        Route::get('/ICA/retenedor/pdf/{id}', 'Impuestos\ICA\IcaController@facturaRetenedor')->name('impuestos.icaRetenedor.factura');
        Route::get('/ICA/retenedor/form/{id}', 'Impuestos\ICA\IcaController@formRetenedor')->name('impuestos.icaRetenedor.formulario');
        Route::delete('/ICA/retenedor/delete/{idForm}/{idPay}', 'Impuestos\ICA\IcaController@deleteRetenedor');

            //EXOGENA
        Route::get('/ICA/exogena/create','Impuestos\ICA\IcaController@createExogena')->name('impuestos.icaExogena.create');
        Route::post('/ICA/exogena', 'Impuestos\ICA\IcaController@storeExogena')->name('impuestos.icaExogena.store');
        Route::delete('/ICA/exogena/delete/{id}', 'Impuestos\ICA\IcaController@deleteExogena');

        //PREDIAL
        Route::get('/PREDIAL/create','Impuestos\Predial\PredialController@create')->name('impuestos.predial.create');
        Route::post('/PREDIAL','Impuestos\Predial\PredialController@store')->name('impuestos.predial.store');
        Route::post('/PREDIAL/calendario','Impuestos\Predial\PredialController@getImpCalendar');
        Route::post('/PREDIAL/predio','Impuestos\Predial\PredialController@getPredio');
        Route::post('/PREDIAL/liquidar','Impuestos\Predial\PredialController@liquidar');
        Route::post('/PREDIAL/uvt','Impuestos\Predial\PredialController@uvt');
        Route::get('/PREDIAL/pdf/{id}', 'Impuestos\Predial\PredialController@factura')->name('impuestos.predial.factura');
        Route::get('/PREDIAL/form/{id}', 'Impuestos\Predial\PredialController@form')->name('impuestos.predial.formulario');


        //PAGOS
        //VALIDAR SI EL PAGO SE PUEDE DESCARGAR
        Route::post('/Pagos/validatePay','Impuestos\Pagos\PagosController@validatePagoDownload');
        Route::post('/Pagos/deletePay','Impuestos\Pagos\PagosController@deletePago');
        Route::post('/Pagos/confirmPay','Impuestos\Pagos\PagosController@confirmPay');

        //DESCARGAR PAZ Y SALVO
        Route::get('/Pagos/certPyS/{id}','Impuestos\Pagos\PagosController@certDownload');

        Route::get('/Pagos/{modulo}', 'Impuestos\Pagos\PagosController@index');
        Route::resource('/Pagos', 'Impuestos\Pagos\PagosController');
        Route::post('/Pagos/Send', 'Impuestos\Pagos\PagosController@Send');
        Route::post('/Pagos/constancia', 'Impuestos\Pagos\PagosController@Constancia');

        //CARGAR PAGO DESDE ADMIN
        Route::post('/Pagos/constancia/admin', 'Impuestos\Pagos\PagosController@ConstanciaAdmin');

        //EMAIL
        Route::get('/sparkpost', function () {
            Mail::send('impuestos.emails.test', [], function ($message) {
                $message
                    ->from('from@yourdomain.com', 'Your Name')
            ->to('to@otherdomain.com', 'Receiver Name')
            ->subject('From SparkPost with ❤');
            });
        });
    });

	////////////////////admin//////////////////
	Route::group(['prefix' => 'dashboard'] ,function () 
	{
		Route::get('notificaciones', 'NotificationController@index')->name('notificaciones.index');

		Route::get('notificaciones/{id}', 'NotificationController@read')->name('notifications.read');
		Route::delete('notificaciones/{id}', 'NotificationController@destroy')->name('notifications.destroy');
		Route::get('notificaciones-visibilidad/{id}', 'NotificationController@visibilidad')->name('notification.visibilidad');
		
		Route::resource('terceros', 'Administrativo\TercerosController');
		Route::resource('contractual', 'Administrativo\Contractual\ContractualController');
		Route::resource('administrativos', 'Sansonatorio\AdministrativoController');
		Route::resource('comisariafamilia', 'Convivencia\ComisariaFamiliaController');
		Route::resource('comiteconciliacion', 'Judicial\ComiteConsiliacionController');
		Route::resource('comparendos', 'Convivencia\ComparendoController');

        //RUTAS COMISIONES
        Route::get('/comision/{id}/','Administrativo\GestionDocumental\Comisiones\ComisionesController@index');

        //RUTAS alcaldia

        Route::Resource('alcaldia','Administrativo\GestionDocumental\AlcaldiaController');


        //RUTAS CONCEJALES

        Route::Resource('concejales','Administrativo\GestionDocumental\ConcejalController');
        Route::get('/concejales/create','Administrativo\GestionDocumental\ConcejalController@create');

        //RUTAS MESA DIRECTIVA

        Route::Resource('/mesaDir/','Administrativo\GestionDocumental\MesaDirectivaController');

		Route::resource('demandante', 'Judicial\DemandanteController');
		Route::resource('demandado', 'Judicial\DemandadoController');
		Route::resource('disciplinarios', 'Sansonatorio\DisciplinarioController');
		Route::resource('licenciasplaneacion', 'Planeacion\LicenciaPlaneacionController');
		Route::resource('maquinaria', 'Infraestructura\MaquinariaController');
		Route::resource('pazysalvo', 'Administrativo\PazYSalvoController');
		Route::resource('planmejoramiento', 'Auditoria\ControlInterno\PlanMejoramientoController');
		Route::resource('podaarboles', 'Administrativo\MedioAmbiente\PodaArbolController');
		Route::resource('policivo', 'Convivencia\PolicivoController');
		Route::resource('titulacionpredios', 'Administrativo\Vivienda\TitulacionPredioController');
		Route::resource('subirArchivo', 'SubirArchivoController');
		Route::resource('subirArchivoContractual', 'Administrativo\Contractual\SubirArchivoContractualController');
		Route::resource('rutas', 'Configuracion\Rutas\RutaController');
		Route::get('ruta-orden/{id}', 'Configuracion\Rutas\RutaController@rutaOrden')->name('ruta.orden');
		Route::get('ruta-orden/{id}/create', 'Configuracion\Rutas\RutaController@rutaOrdenCreate')->name('ruta.orden.create');
		Route::post('ruta-orden/', 'Configuracion\Rutas\RutaController@rutaOrdenStore')->name('ruta.orden.store');
		Route::get('ruta-orden/{ruta}/edit/{id}', 'Configuracion\Rutas\RutaController@rutaOrdenEdit')->name('ruta.orden.edit');
		Route::put('ruta-orden/{ruta}/update/{id}', 'Configuracion\Rutas\RutaController@rutaOrdenUpdate')->name('ruta.orden.update');
		Route::delete('ruta-orden/{ruta}/delete/{id}', 'Configuracion\Rutas\RutaController@rutaOrdenDestroy')->name('ruta.orden.delete');

	});

	Route::group(['prefix' => 'administrativo'] ,function () 
	{

        //TESORERIA

	           //Comprobante de Egresos

        Route::get('CIngresos/{id}', 'Administrativo\ComprobanteIngresos\ComprobanteIngresosController@index');
        Route::get('CIngresos/create/{id}', 'Administrativo\ComprobanteIngresos\ComprobanteIngresosController@create');
        Route::get('CIngresos/show/{id}', 'Administrativo\ComprobanteIngresos\ComprobanteIngresosController@show');
        Route::get('CIngresos/pdf/{id}', 'Administrativo\ComprobanteIngresos\ComprobanteIngresosController@pdf');
        Route::get('CIngresos/fin/{estado}/{id}', 'Administrativo\ComprobanteIngresos\ComprobanteIngresosController@estados');
        Route::delete('CIngresos/{vigen}/{id}/delete', 'Administrativo\ComprobanteIngresos\ComprobanteIngresosController@destroy');
        Route::resource('CIngresos', 'Administrativo\ComprobanteIngresos\ComprobanteIngresosController');
        Route::post('CIRubro', 'Administrativo\ComprobanteIngresos\ComprobanteIngresosController@rubroStore');
        Route::delete('CIRubro/{id}/delete', 'Administrativo\ComprobanteIngresos\ComprobanteIngresosController@rubroDelete');

            //Bancos
        Route::get('tesoreria/bancos/libros','Administrativo\Tesoreria\BancosController@libros');
        Route::get('tesoreria/bancos/conciliacion','Administrativo\Tesoreria\BancosController@conciliacion');
        Route::get('tesoreria/bancos/conciliacion/{conciliacion_id}','Administrativo\Tesoreria\BancosController@conciliacion_pdf')->name('conciliacion.guardar.pdf');
        Route::get('tesoreria/bancos/conciliacion/{id}/pdf','Administrativo\Tesoreria\BancosController@pdf')->name('conciliacion.pdf');
        Route::get('tesoreria/bancos/libros','Administrativo\Tesoreria\BancosController@libros');
        Route::post('tesoreria/bancos/conciliacion','Administrativo\Tesoreria\BancosController@saveConciliacion');
        Route::post('tesoreria/bancos/movAccount','Administrativo\Tesoreria\BancosController@movAccount');
        Route::post('tesoreria/bancos/movAccountLibros','Administrativo\Tesoreria\BancosController@movAccountLibros');
        Route::post('tesoreria/bancos/makeConciliacion','Administrativo\Tesoreria\BancosController@makeConciliacion');
        Route::resource('bancos','Administrativo\Tesoreria\BancosController');
        Route::post('tesoreria/bancos/conciliacion/guardar-ver','Administrativo\Tesoreria\BancosController@saveAndSeePdf')->name('conciliacion.guardar-ver');
        Route::get('tesoreria/bancos/eliminar-conciliacion/{conciliacion}','Administrativo\Tesoreria\BancosController@eliminar_conciliacion')->name('eliminar.conciliacion');
            //DESCUENTOS
        Route::get('tesoreria/descuentos/{vigencia}','Administrativo\Tesoreria\descuentos\TesoreriaDescuentosController@index');
        Route::get('tesoreria/descuentos/viewpago/{id}/view','Administrativo\Tesoreria\descuentos\TesoreriaDescuentosController@show');
        Route::post('tesoreria/descuentos/movimientos/pagos','Administrativo\Tesoreria\descuentos\TesoreriaDescuentosController@movAccount');
        Route::post('tesoreria/descuentos/makePago','Administrativo\Tesoreria\descuentos\TesoreriaDescuentosController@makePago');
        Route::post('tesoreria/descuentos/store','Administrativo\Tesoreria\descuentos\TesoreriaDescuentosController@store');


            //Pac
        Route::resource('pac','Administrativo\Tesoreria\PacController');


        //ALMACEN

            //Productos

        Route::resource('productos','Administrativo\Almacen\ProductoController');

        //Inventario

        Route::resource('inventario','Administrativo\Almacen\InventarioController');

        //Bienes, muebles e inmuebles

        Route::resource('muebles','Administrativo\Almacen\MueblesController');

        //Comprobante de Salida

        Route::resource('salida','Administrativo\Almacen\SalidaController');



        //Registros

        Route::post('changeObject/rp/{id}/', 'Administrativo\Registro\RegistrosController@changeObject');
        Route::get('registros/{id}', 'Administrativo\Registro\RegistrosController@index');
        Route::get('registros/create/{id}', 'Administrativo\Registro\RegistrosController@create');
        Route::get('registros/show/{id}', 'Administrativo\Registro\RegistrosController@show');
        Route::resource('registros', 'Administrativo\Registro\RegistrosController');
        Route::resource('cdpsRegistro','Administrativo\Registro\CdpsRegistroController');
        Route::resource('cdpsRegistro/valor','Administrativo\Registro\CdpsRegistroValorController');
        Route::get('registros/{id}/{fecha}/{valor}/{estado}/{valTot}/{rol}', 'Administrativo\Registro\RegistrosController@updateEstado');
        Route::post('registros/{id}/anular/', 'Administrativo\Registro\RegistrosController@anular');
        Route::put('registros/r/{id}/{rol}/{estado}/{vigencia}', 'Administrativo\Registro\RegistrosController@rechazar');
        Route::post('registros/{id}/anular/', 'Administrativo\Registro\RegistrosController@anular');

            //pdf registros
		Route::get('/registro/pdf/{id}/{vigen}', 'Administrativo\Registro\RegistrosController@pdf')->name('registro-pdf');


        //CDP's

        Route::post('changeObject/cdp/{id}/', 'Administrativo\Cdp\CdpController@changeObject');
        Route::get('cdp/{id}', 'Administrativo\Cdp\CdpController@index');
        Route::put('cdp/{id}/{vigen}', 'Administrativo\Cdp\CdpController@update');
        Route::get('cdp/create/{id}', 'Administrativo\Cdp\CdpController@create');
        Route::post('cdp/', 'Administrativo\Cdp\CdpController@store');
        Route::get('cdp/{vigen}/{id}', 'Administrativo\Cdp\CdpController@show');
        Route::get('cdp/{vigen}/{id}/edit', 'Administrativo\Cdp\CdpController@edit');
        Route::delete('cdp/{vigen}/{id}/delete', 'Administrativo\Cdp\CdpController@destroy');
        Route::Resource('rubrosCdp','Administrativo\Cdp\RubrosCdpController');
        Route::Resource('rubrosCdp/valor','Administrativo\Cdp\RubrosCdpValorController');
        Route::get('cdp/{id}/{rol}/{fecha}/{valor}/{estado}', 'Administrativo\Cdp\CdpController@updateEstado');
        Route::put('cdp/r/{id}/{vigen}', 'Administrativo\Cdp\CdpController@rechazar');
        Route::post('cdp/{id}/anular/{vigen}', 'Administrativo\Cdp\CdpController@anular');
        Route::post('cdp/check', 'Administrativo\Cdp\CdpController@check');
        //pdf cdp
		Route::get('cdp/pdf/{id}/{vigen}', 'Administrativo\Cdp\CdpController@pdf')->name('cpd-pdf');
		Route::get('cdp/pdfBorrador/{id}/{vigen}', 'Administrativo\Cdp\CdpController@pdfBorrador')->name('cpd-pdf-borrador');
        //Crear cdp con actividad
        Route::post('cdp/{id}/{vigen}/asignActividad', 'Administrativo\Cdp\CdpController@cdpActividad');
        Route::post('cdp/{id}/RestartInv', 'Administrativo\Cdp\CdpController@restaurarInv');
        Route::post('cdp/{id}/DeleteInv', 'Administrativo\Cdp\CdpController@deleteInv');

        //buscar actividades para crear CDP
        Route::post('proyectos/find-actividad', 'Administrativo\Cdp\CdpController@findActividades');

        Route::resource('marcas-herretes', 'Administrativo\MarcaHerrete\MarcaHerreteController');
        Route::get('persona-find/{identificador}', 'Cobro\PersonasController@personaFind');
        Route::post('persona/find-create', 'Cobro\PersonasController@PersonafindCreate');

        //ORDENES DE PAGO

        Route::get('ordenPagos/{id}','Administrativo\OrdenPago\OrdenPagosController@index');
        Route::get('ordenPagos/show/{id}','Administrativo\OrdenPago\OrdenPagosController@show');
        Route::get('ordenPagos/create/{id}','Administrativo\OrdenPago\OrdenPagosController@create');
        Route::resource('ordenPagos','Administrativo\OrdenPago\OrdenPagosController');
        Route::get('ordenPagos/liquidacion/create/{id}','Administrativo\OrdenPago\OrdenPagosController@liquidacion');
        Route::put('ordenPagos/liquidacion/store','Administrativo\OrdenPago\OrdenPagosController@liquidar');
        Route::get('ordenPagos/descuento/create/{id}','Administrativo\OrdenPago\OrdenPagosDescuentosController@create');
        Route::resource('ordenPagos/descuento','Administrativo\OrdenPago\OrdenPagosDescuentosController');
        Route::get('ordenPagos/pay/create/{id}','Administrativo\OrdenPago\OrdenPagosController@pay');
        Route::put('ordenPagos/pay/store','Administrativo\OrdenPago\OrdenPagosController@paySave');
        Route::get('ordenPagos/monto/create/{id}','Administrativo\OrdenPago\OrdenPagosRubrosController@create');
        Route::put('ordenPagos/monto/store','Administrativo\OrdenPago\OrdenPagosRubrosController@store');
        Route::delete('ordenPagos/descuento/rf/{id}','Administrativo\OrdenPago\OrdenPagosController@deleteRF');
        Route::delete('ordenPagos/descuento/m/{id}','Administrativo\OrdenPago\OrdenPagosController@deleteM');
        Route::delete('ordenPagos/puc/delete/{id}','Administrativo\OrdenPago\OrdenPagosController@deleteP');
        Route::put('ordenPagos/monto/delete','Administrativo\OrdenPago\OrdenPagosRubrosController@massiveDelete');
        Route::post('ordenPagos/{id}/anular/', 'Administrativo\OrdenPago\OrdenPagosController@anular');
        //PDF OrdenPago y ComprobanteEgresos
        Route::get('ordenPagos/pdf/{id}','Administrativo\OrdenPago\OrdenPagosController@pdf_OP')->name('op-pdf');
        Route::get('egresos/pdf/{id}','Administrativo\OrdenPago\OrdenPagosController@pdf_CE')->name('ce-pdf');
        //EMBARGOS DE LAS ORDENES DE PAGO
        Route::get('tesoreria/ordenPagos/embargos/{id}','Administrativo\OrdenPago\OrdenPagosController@embargos')->name('op-embargos');
        Route::post('tesoreria/ordenPagos/embargos/getOP/find','Administrativo\OrdenPago\OrdenPagosController@getOPEmbargo');
        Route::post('tesoreria/ordenPagos/embargos/make','Administrativo\OrdenPago\OrdenPagosController@getEmbargo');

        //PAGOS

        Route::post('changeCheque/pago/{id}/', 'Administrativo\Pago\PagosController@changeCheque');
        Route::delete('pagos/{id}/{vigencia}', 'Administrativo\Pago\PagosController@delete')->name('pago-delete');
        Route::get('pagos/{id}', 'Administrativo\Pago\PagosController@index');
        Route::get('pagos/create/{id}', 'Administrativo\Pago\PagosController@create');
        Route::get('pagos/show/{id}', 'Administrativo\Pago\PagosController@show');
        Route::resource('pagos', 'Administrativo\Pago\PagosController');
        Route::get('pagos/asignacion/{id}','Administrativo\Pago\PagosController@asignacion');
        Route::put('pagos/asignacion/store','Administrativo\Pago\PagosController@asignacionStore');
        Route::put('pagos/asignacion/delete','Administrativo\Pago\PagosController@asignacionDelete');
        Route::get('pagos/banks/{id}','Administrativo\Pago\PagosController@bank');
        Route::put('pagos/banks/store','Administrativo\Pago\PagosController@bankStore');
        Route::post('pagos/{id}/anular/', 'Administrativo\Pago\PagosController@anular');

        //CONTABILIDAD


            //Configuración

        Route::resource('contabilidad/config','Administrativo\Contabilidad\ContaConfigController');

            //Retención en la Fuente
                //DECLARACION DE LA RETENCION EN LA FUENTE
        Route::get('tesoreria/retefuente/declaracion','Administrativo\Tesoreria\retefuente\DeclaracionController@index');

                //GENERAR CERTIFICADO DE LA RETENCION EN LA FUENTE
        Route::get('tesoreria/retefuente/certificado','Administrativo\Tesoreria\retefuente\CertificadoController@index');
        Route::post('tesoreria/retefuente/certificado','Administrativo\Tesoreria\retefuente\CertificadoController@getCert');


                //PAGO RETENCION EN LA FUENTE
        Route::get('tesoreria/retefuente/pago/{vigencia_id}/{mes}','Administrativo\OrdenPago\RetencionFuente\RetencionFuenteController@pagoRetefuente');
        Route::get('tesoreria/retefuente/pago/{vigencia_id}','Administrativo\OrdenPago\RetencionFuente\RetencionFuenteController@pagosRetefuente');
        Route::get('tesoreria/retefuente/viewpago/{id}/view','Administrativo\OrdenPago\RetencionFuente\RetencionFuenteController@showpago');
        Route::get('tesoreria/retefuente/PDFpago/{id}/PDF','Administrativo\OrdenPago\RetencionFuente\RetencionFuenteController@pdfPago');
        Route::post('tesoreria/retefuente/pago/{vigencia_id}/{mes}/make','Administrativo\OrdenPago\RetencionFuente\RetencionFuenteController@makePagoRetefuente');

        Route::resource('contabilidad/retefuente','Administrativo\OrdenPago\RetencionFuente\RetencionFuenteController');
        Route::get('contabilidad/retefuente/create','Administrativo\OrdenPago\RetencionFuente\RetencionFuenteController@create');


        //NOTAS CREDITO
        Route::get('tesoreria/notasCredito','Administrativo\Tesoreria\NotaCreditoController@index');
        Route::post('tesoreria/notasCredito','Administrativo\Tesoreria\NotaCreditoController@store');
        Route::get('tesoreria/notasCredito/create','Administrativo\Tesoreria\NotaCreditoController@create');
        Route::get('tesoreria/notasCredito/show/{id}','Administrativo\Tesoreria\NotaCreditoController@show');

            //Impuestos Municipales

        Route::resource('contabilidad/impumuni','Administrativo\OrdenPago\DescMunicipales\DescMunicipalesController');
        Route::get('contabilidad/impumuni/create','Administrativo\OrdenPago\DescMunicipales\DescMunicipalesController@create');

            //PUC

        Route::resource('contabilidad/puc/pucIndexAct','Administrativo\Contabilidad\ActPucController');
        Route::resource('contabilidad/puc/pucIndex','Administrativo\Contabilidad\ResPucController');
        

        Route::resource('contabilidad/puc','Administrativo\Contabilidad\PucController');
        Route::get('contabilidad/puc/create','Administrativo\Contabilidad\PucController@create');

        Route::resource('contabilidad/puc/level','Administrativo\Contabilidad\LevelPUCController');
        Route::get('contabilidad/puc/level/create/{id}','Administrativo\Contabilidad\LevelPUCController@create');

        Route::get('contabilidad/puc/registers/create/{vigencia}/{level}','Administrativo\Contabilidad\RegistersPucController@create');
        Route::resource('contabilidad/puc/registers','Administrativo\Contabilidad\RegistersPucController');

        Route::resource('contabilidad/puc/rubro','Administrativo\Contabilidad\RubrosPucController');
        Route::get('contabilidad/puc/rubro/create/{vigencia}','Administrativo\Contabilidad\RubrosPucController@create');

            //Informes

        Route::resource('contabilidad/informes','Administrativo\Contabilidad\ReportsController');
        Route::get('contabilidad/informes/lvl/{id}','Administrativo\Contabilidad\ReportsController@lvl');
        Route::get('contabilidad/informes/rubros/{id}','Administrativo\Contabilidad\ReportsController@rubros');

        //Impuestos Predial

        Route::get('impuestospredial/liquidador/make','Administrativo\ImpuestosPredial\LiquidadorController@make');
        Route::post('impuestospredial/liquidador/make','Administrativo\ImpuestosPredial\LiquidadorController@makeLiquidacion');
        Route::resource('impuestospredial/liquidador','Administrativo\ImpuestosPredial\LiquidadorController');

        //Impuestos

            //MUELLAJE
        Route::get('impuestos/muellaje/{id}/formulario/pdf','Administrativo\Impuestos\MuellajeController@formulario');
        Route::post('impuestos/muellaje/deleteMuellaje/{id}', 'Administrativo\Impuestos\MuellajeController@deleteMuellaje');
        Route::post('impuestos/muellaje/{id}/find','Administrativo\Impuestos\MuellajeController@findAtraque');
        Route::get('impuestos/muellaje/edit/{id}','Administrativo\Impuestos\MuellajeController@edit');
        Route::resource('impuestos/muellaje','Administrativo\Impuestos\MuellajeController');
        Route::post('impuestos/muellaje/pay','Administrativo\Impuestos\MuellajeController@pay');
        Route::delete('impuestos/muellaje/vehiculo/delete/{id}', 'Administrativo\Impuestos\MuellajeController@deleteVehiculo');

            //DELINEACION Y URBANISMO
        Route::resource('impuestos/delineacion','Administrativo\Impuestos\DelineacionController');
        Route::post('impuestos/delineacion/pay','Administrativo\Impuestos\DelineacionController@pay');
        Route::delete('impuestos/delineacion/vecino/delete/{id}', 'Administrativo\Impuestos\DelineacionController@deleteVecino');
        Route::delete('impuestos/delineacion/titular/delete/{id}', 'Administrativo\Impuestos\DelineacionController@deleteTitular');

        //OBTENER LOS USUARIOS QUE NO HAN PAGADO PREDIAL NI ICA
        Route::get('impuestos/admin/noPayUsers','Administrativo\Impuestos\ImpAdminController@noPay');

        //ADMINISTRACION DE IMPUESTOS
        Route::resource('impuestos/admin','Administrativo\Impuestos\ImpAdminController');

            //ADMINISTRACION DE USUARIOS DE PREDIAL
            Route::get('impuestos/admin/predial/user/edit/{id}','Administrativo\Impuestos\ImpAdminController@editUser');
            Route::put('impuestos/admin/predial/user/{id}','Administrativo\Impuestos\ImpAdminController@updateUser');

        //COMUNICADOS
        Route::get('impuestos/comunicado/create','Administrativo\Impuestos\ImpAdminController@makeComunicado');
        Route::post('impuestos/comunicado/make','Administrativo\Impuestos\ImpAdminController@generateComunicado');
        Route::get('impuestos/comunicado/{id}','Administrativo\Impuestos\ImpAdminController@showComunicado');

        //LIBROS
        Route::post('contabilidad/libros/rubros_puc','Administrativo\Contabilidad\LibrosController@getRubrosPUC');
        Route::resource('contabilidad/libros','Administrativo\Contabilidad\LibrosController');

        //BALANCES
        Route::resource('contabilidad/balances/prueba','Administrativo\Contabilidad\Balances\PruebaController');

	});


	Route::group(['prefix' => 'admin'] ,function () 
	{

        //crud Configuración General
        Route::resource('configGeneral','Admin\ConfigGeneralController');
        Route::post('configGeneral/imgProy','Admin\ConfigGeneralController@newImgProy');

        //crud Entidades
        Route::resource('entidades','Admin\EntidadesController');

        //crud Modulos
        Route::resource('modulos','Admin\ModulosController');

        //crud Permisos
        Route::resource('permisos','Admin\PermissionController');

	    //AUDITS
        Route::resource('audits','Admin\AuditsController');

		//crud funcionarios
		Route::get('funcionarios/jefes/{id}', 'Admin\FuncionariosController@jefe');
		Route::resource('funcionarios', 'Admin\FuncionariosController');

		//crud de roles
		Route::resource('roles', 'Admin\RolesController');

		//crud dependencias
		Route::resource('dependencias', 'Admin\DependenciasController');
		Route::get('dependencias/listar', 'Admin\DependenciasController@listar')->name('dependencias.listar');
		
        //RUTAS ORDEN DEL DIA

        Route::resource('ordenDia','Admin\OrdenDiaController');

		////////////////////////////////////////////////////
		//Route::resource('personas', 'PersonasController');
		//Route::get('asignar/{id}', 'AsignarController@index');
		//Route::resource('asignar', 'AsignarController');
		/*
		Route::get('predios-sin-asignar', 'PredioController@predioSinAsignar')->name('unnassigned');
		Route::get('predios-asignados', 'PredioController@predioAsignado')->name('assignor');
		Route::post('predios-asignar', 'PredioController@predioAsignarAdministrativeStore')->name('assignor.store');
		Route::get('predio-expediente/{id}', 'PredioController@asignarExpediente')->name('assignor.expedient');

		Route::post('predio-asignar', 'PersonaPredioController@predioAsignarPersona');
		*/
		

	    //Route::post('importar', 'ImportController@import')->name('importar');

	    //Route::resource('procesos','ProcesoController');

	    //Route::post('proceso-upload-file','ProcesoController@uploadFile')->name('proceso.upload.file');

	});

	////// RUTAS PRESUPUESTO


    //// HISTORICO
    Route::get('presupuesto/historico/{id}', 'Hacienda\Presupuesto\VigenciaController@historico');
    #Route::get('presupuesto', 'Hacienda\Presupuesto\Egresos\IndexController@index')->name('presupuesto.index');
    Route::get('presupuesto', 'Hacienda\Presupuesto\Egresos\IndexController@newPrepLoad')->name('presupuesto.index');
    Route::post('presupuesto/getPrepSaved', 'Hacienda\Presupuesto\Egresos\IndexController@getPrepSaved');
    Route::get('presupuesto/refreshPrepSaved', 'Hacienda\Presupuesto\Egresos\IndexController@refreshPrepSaved');
	Route::get('presupuesto/vigencia/create/{tipo}', 'Hacienda\Presupuesto\VigenciaController@create');
	Route::resource('presupuesto/vigencia', 'Hacienda\Presupuesto\VigenciaController');
	Route::get('presupuesto/level/create/{vigencia}', 'Hacienda\Presupuesto\LevelController@create');
	Route::resource('presupuesto/level', 'Hacienda\Presupuesto\LevelController');
	Route::get('presupuesto/registro/create/{vigencia}/{level}', 'Hacienda\Presupuesto\RegistroController@create');
	Route::resource('presupuesto/registro', 'Hacienda\Presupuesto\RegistroController');
	Route::get('presupuesto/font/create/{vigencia}', 'Hacienda\Presupuesto\FontsController@create');
	Route::resource('presupuesto/font', 'Hacienda\Presupuesto\FontsController');
	Route::get('presupuesto/rubro/create/{vigencia}', 'Hacienda\Presupuesto\RubrosController@create');
    Route::delete('presupuesto/rubro/{id}/{vigencia}', 'Hacienda\Presupuesto\RubrosController@deleteRubro');
	Route::post('presupuesto/findFontDep', 'Hacienda\Presupuesto\RubrosController@findFont');
	Route::resource('presupuesto/rubro', 'Hacienda\Presupuesto\RubrosController');
    Route::put('presupuesto/rubro/m/{m}/{id}', 'Hacienda\Presupuesto\RubrosMovController@movimiento');
	Route::resource('presupuesto/FontRubro', 'Hacienda\Presupuesto\FontRubroController');
	Route::resource('presupuesto/FontRubro/saldo', 'Hacienda\Presupuesto\FontRubroController@saldoFont');
        //ASIGNAR ACTIVIDAD AL PROYECTO
        Route::post('presupuesto/proyectos/asignaRubroActiv','Hacienda\Presupuesto\Egresos\IndexController@asignaRubroProyecto');

        //VER ACTIVIDAD
        Route::get('presupuesto/actividades/{vigencia}','Hacienda\Presupuesto\Egresos\ActividadController@index');
        Route::get('presupuesto/actividad/{id}/{vigencia}','Hacienda\Presupuesto\Egresos\ActividadController@show');

        //CERTIFICADO DEL PROYECTO
        Route::get('presupuesto/proyecto/{code}','Hacienda\Presupuesto\Egresos\ActividadController@certProyecto');

    //INFORMES PRESUPUESTO EGRESOS

    Route::get('presupuesto/CHIPEgresosProg/{inicio}/{final}','Hacienda\Presupuesto\Informes\InformesCHIPController@makeEgresosProg');
    Route::get('presupuesto/CHIPEgresosExec/{inicio}/{final}','Hacienda\Presupuesto\Informes\InformesCHIPController@makeEgresosExec');
    Route::get('presupuesto/CHIPIngresosExec/{inicio}/{final}','Hacienda\Presupuesto\Informes\InformesCHIPController@makeIngresosExec');
    Route::get('presupuesto/CHIPIngresosProg/{inicio}/{final}','Hacienda\Presupuesto\Informes\InformesCHIPController@makeIngresosProg');
    Route::get('presupuesto/informeGeneralEgresosEXCEL','Hacienda\Presupuesto\Informes\InformeController@makeEgresosEXCEL');
    Route::get('presupuesto/makeEgresosEjecucion/{inicio}/{final}','Hacienda\Presupuesto\Informes\InformeController@makeEgresosEjecucion');
    Route::get('presupuesto/informeGeneralIngresosEXCEL','Hacienda\Presupuesto\Informes\InformeController@makeIngresosEXCEL');
    Route::get('presupuesto/informeGeneralEgresosPDF','Hacienda\Presupuesto\Informes\InformeController@makeEgresosPDF');
    Route::get('presupuesto/informeGeneralIngresosPDF','Hacienda\Presupuesto\Informes\InformeController@makeIngresosPDF');
    Route::get('presupuesto/makeIngresosEjecucion/{inicio}/{final}','Hacienda\Presupuesto\Informes\InformeController@makeIngresosEjecucion');
    Route::get('presupuesto/makeCDPsEXCEL','Hacienda\Presupuesto\Informes\InformeDocsController@makeCDPsEXCEL');
    Route::get('presupuesto/makeRPsEXCEL','Hacienda\Presupuesto\Informes\InformeDocsController@makeRPsEXCEL');
    Route::get('presupuesto/makePagosEXCEL','Hacienda\Presupuesto\Informes\InformeDocsController@makePagosEXCEL');
    Route::get('presupuesto/makeOrdenPagosEXCEL','Hacienda\Presupuesto\Informes\InformeDocsController@makeOrdenPagosEXCEL');
    Route::get('presupuesto/makeCCEXCEL','Hacienda\Presupuesto\Informes\InformeDocsController@makeCompContEXCEL');
    Route::resource('presupuesto/informes','Hacienda\Presupuesto\Informes\ReportsController');
    Route::get('presupuesto/informes/lvl/{id}/{vigencia}','Hacienda\Presupuesto\Informes\ReportsController@lvl');
    Route::get('presupuesto/informes/rubros/{id}','Hacienda\Presupuesto\Informes\ReportsController@rubros');
    Route::get('presupuesto/informes/contractual/homologar/{id}','Hacienda\Presupuesto\Informes\Contractual\CodeContractualesController@index');
    Route::resource('presupuesto/informes/contractual/homologar','Hacienda\Presupuesto\Informes\Contractual\CodeContractualesController');
    Route::get('presupuesto/informes/contractual/homologar/{id}/create','Hacienda\Presupuesto\Informes\Contractual\CodeContractualesController@create');
    Route::put('presupuesto/informes/contractual/reporte','Hacienda\Presupuesto\Informes\Contractual\CodeContractualesController@report');
    Route::get('presupuesto/informes/contractual/asignar','Hacienda\Presupuesto\Informes\Contractual\CodeContractualesController@rubros');
    Route::put('presupuesto/informes/contractual/asignar/store', 'Hacienda\Presupuesto\Informes\Contractual\CodeContractualesController@rubroStore');

    //// EJECUCION PRESUPUESTAL

    Route::put('presupuesto/ejecucion/gastos/{vigencia}','Hacienda\Presupuesto\Informes\ReportsController@ejecuTrimG');
    Route::get('presupuesto/ejecucion/gastos/{vigencia}/{fechaInicio}/{fechaFin}','Hacienda\Presupuesto\Informes\ReportsController@ejecuTrimGastosHistorico');
    Route::get('presupuesto/ejecucion/gastosTot/{vigencia}','Hacienda\Presupuesto\VigenciaController@historicoExcel');


    // RUTAS DEL PRESUPUESTO DEL SIGUIENTE AÑO
    Route::get('newPre/{type}/{year}','Hacienda\Presupuesto\PresupuestoController@newPre');


    ////// RUTAS PRESUPUESTO INGRESOS
    Route::get('presupuestoIng','Hacienda\Presupuesto\PresupuestoController@ingresos');
    ///// RUTAS DEL PRESUPUESTO DEL SIGUIENTE AÑO
    Route::get('newPreIng/{type}/{year}','Hacienda\Presupuesto\PresupuestoController@newPreIng');

    ////// RUTAS DEL PRESUPUESTO PARA EL CUIPO
    Route::get('presupuesto/rubro/CUIPO/{paso}/{vigencia}', 'Hacienda\Presupuesto\CuipoController@index');
    Route::get('presupuesto/rubro/CUIPO/CPC/{id}/{vigencia}/DELETE', 'Hacienda\Presupuesto\CuipoController@deleteCPCRubro');
    Route::get('presupuesto/rubro/CUIPO/CPC/{id}/{vigencia}/DELETEALL', 'Hacienda\Presupuesto\CuipoController@deleteAllCPCRubro');
    Route::post('presupuesto/rubro/CUIPO/CPC', 'Hacienda\Presupuesto\CuipoController@saveCPC');
    Route::post('presupuesto/rubro/CUIPO/SourceFundings', 'Hacienda\Presupuesto\CuipoController@saveSourceFundings');
    Route::get('presupuesto/rubro/CUIPO/SourceFundings/{id}/{vigencia}/DELETE', 'Hacienda\Presupuesto\CuipoController@deleteFontRubro');
    Route::get('presupuesto/rubro/CUIPO/SourceFundings/{id}/{vigencia}/DELETEALL', 'Hacienda\Presupuesto\CuipoController@deleteAllFontsRubro');
    Route::post('presupuesto/rubro/CUIPO/TipoNormas', 'Hacienda\Presupuesto\CuipoController@saveTipoNorma');
    Route::post('presupuesto/rubro/CUIPO/Terceros', 'Hacienda\Presupuesto\CuipoController@saveTercero');
    Route::post('presupuesto/rubro/CUIPO/PoliticaPublica', 'Hacienda\Presupuesto\CuipoController@savePP');
    Route::post('presupuesto/rubro/CUIPO/BudgetSection', 'Hacienda\Presupuesto\CuipoController@saveBS');
    Route::post('presupuesto/rubro/CUIPO/VigenciaGastos', 'Hacienda\Presupuesto\CuipoController@saveVG');
    Route::post('presupuesto/rubro/CUIPO/Sectors', 'Hacienda\Presupuesto\CuipoController@saveSec');

    /////RUTA DE ASIGNAR DINERO A LA DEPENDENCIA DEL RUBRO
    Route::post('presupuesto/rubro/dineroDependencia/{id}', 'Hacienda\Presupuesto\RubrosController@asignarDineroDep');


    ////// RUTAS PLAN DE DESARROLLO
	Route::resource('pdd','Planeacion\Pdd\PdesarrolloController');
	Route::get('pdd/data/create/{pdd}','Planeacion\Pdd\EjesController@create');
	Route::resource('pdd/eje','Planeacion\Pdd\EjesController');
	Route::resource('pdd/programa','Planeacion\Pdd\ProgramasController');
	Route::get('pdd/proyecto/create/{pdd}','Planeacion\Pdd\ProyectosController@create');
	Route::resource('pdd/proyecto','Planeacion\Pdd\ProyectosController');
	Route::get('pdd/subproyecto/create/{pdd}','Planeacion\Pdd\SubproyectosController@create');
	Route::resource('pdd/subproyecto','Planeacion\Pdd\SubproyectosController');
	Route::get('pdd/producto/create/{pdd}','Planeacion\Pdd\ProductoController@create');
	Route::resource('pdd/producto','Planeacion\Pdd\ProductoController');
	Route::get('pdd/periodo/create/{producto}','Planeacion\Pdd\PeriodoController@create');
	Route::resource('pdd/periodo','Planeacion\Pdd\PeriodoController');

	////// RUTAS CONTRACTUAL

	// Route::get('/nuevaEntrada','Hacienda\Almacen\AlmacenController@nuevaEntrada');
	// Route::get('/inventarioEntradas','Hacienda\Almacen\AlmacenController@inventarioEntradas');
	// Route::get('/inventarioSalidas','Hacienda\Almacen\AlmacenController@inventarioSalidas');
	// Route::get('/entradas','Hacienda\Almacen\AlmacenController@entradas');
	Route::get('contractual/rubros','Administrativo\Contractual\VerRubrosController@index');
	Route::Resource('contractual','Administrativo\Contractual\ContractualController');
    Route::get('contractual/{id}/anexos', 'Administrativo\Contractual\ContractualController@anexos')->name('contractual.anexos');
    Route::get('contractual/{id}/anexos/create', 'Administrativo\Contractual\ContractualController@anexosCreate')->name('contractual.anexosCreate');
    Route::post('contractual/{id}/anexos', 'Administrativo\Contractual\ContractualController@anexosStore');
    Route::get('contractual/anexos/{id}/edit', 'Administrativo\Contractual\ContractualController@anexosEdit')->name('contractual.anexosEdit');
    Route::post('contractual/anexos/{id}', 'Administrativo\Contractual\ContractualController@anexosUpdate');
    Route::delete('contractual/anexos/{id}', 'Administrativo\Contractual\ContractualController@anexosDelete');




});
