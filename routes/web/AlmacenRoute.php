<?php

Route::group([ 'middleware' => 'auth', 'prefix' => 'almacen'] ,function(){
    Route::get('/inventario', "Almacen\ArticuloController@index")->name('almacen.inventario');
    Route::get('/articulo/ajax/{articulo_code}', "Almacen\ArticuloController@articulo_ajax");

    Route::get('/comprobante-ingresos', "Almacen\ComprobanteIngresoController@create")->name('almacen.comprobante.ingreso');
    Route::put('/comprobante-ingresos/{ingreso}', "Almacen\ComprobanteIngresoController@update")->name('almacen.ingreso.update');
    Route::get('/comprobante-ingresos/{ingreso}', "Almacen\ComprobanteIngresoController@show")->name('almacen.ingreso.show');
    Route::get('/comprobante-ingresos/pdf/{ingreso}', "Almacen\ComprobanteIngresoController@pdf")->name('almacen.comprobante.ingreso.pdf');
    Route::post('comprobante-ingreso-import', 'Almacen\ComprobanteIngresoController@import')->name('almacen.entrada.import');
    
    Route::get('/comprobante-egresos', "Almacen\ComprobanteEgresoController@create")->name('almacen.comprobante.egreso');
    Route::put('/comprobante-egresos/{egreso}', "Almacen\ComprobanteEgresoController@update")->name('almacen.egreso.update');
    Route::get('/comprobante-egresos/{egreso}', "Almacen\ComprobanteEgresoController@show")->name('almacen.egreso.show');

    Route::get('/articulo/mantenimiento/{articulo}', "Almacen\ArticuloMantenimientoController@listar")->name('almacen.articulo.mantenimiento');
    Route::post('/articulo/mantenimiento/{articulo}', "Almacen\ArticuloMantenimientoController@store")->name('almacen.articulo.mantenimiento.store');

    Route::get('/create', "Almacen\AlmacenController@create")->name('almacen.create');
    Route::post('/', "Almacen\AlmacenController@store")->name('almacen.store');
    Route::get('/{articulo}/edit', "Almacen\AlmacenController@edit")->name('almacen.edit');
    Route::put('/{articulo}', "Almacen\AlmacenController@update")->name('almacen.update');


   // Route::get('/comprobante-egreso-dependencia/{egreso}/edit', "Almacen\ComprobanteEgresoController@dependencia_edit")->name('almacen.salida.autorizar.dependencia');
   Route::post('/articulo-salida/autorizar/{articulo_salida}', "Almacen\ComprobanteEgresoArticuloController@autorizar")->name('almacen.aticulo.salida.autorizar');


});