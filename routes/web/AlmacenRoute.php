<?php

Route::group([ 'middleware' => 'auth', 'prefix' => 'almacen'] ,function(){
    Route::get('/', "Almacen\AlmacenController@index")->name('almacen.index');
    Route::get('/comprobante-ingresos', "Almacen\AlmacenController@ingresos")->name('almacen.ingreso');
    Route::get('/comprobante-egresos', "Almacen\AlmacenController@egresos")->name('almacen.egreso');

    Route::put('/comprobante-ingresos/{factura}', "Almacen\FacturaController@update")->name('almacen.ingreso.update');
    //Route::put('/comprobante-egresos', "Almacen\FacturaController@update")->name('almacen.ingreso.update');

    Route::get('/create', "Almacen\AlmacenController@create")->name('almacen.create');
    Route::post('/', "Almacen\AlmacenController@store")->name('almacen.store');
    Route::get('/{articulo}/edit', "Almacen\AlmacenController@edit")->name('almacen.edit');
    Route::put('/{articulo}', "Almacen\AlmacenController@update")->name('almacen.update');
});