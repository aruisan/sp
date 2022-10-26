<?php

Route::group([ 'middleware' => 'auth', 'prefix' => 'almacen'] ,function(){
    Route::get('/', "Almacen\AlmacenController@index")->name('almacen.index');
    Route::get('/create', "Almacen\AlmacenController@create")->name('almacen.create');
    Route::post('/', "Almacen\AlmacenController@store")->name('almacen.store');
    Route::get('/{articulo}/edit', "Almacen\AlmacenController@edit")->name('almacen.edit');
    Route::put('/{articulo}', "Almacen\AlmacenController@update")->name('almacen.update');
});