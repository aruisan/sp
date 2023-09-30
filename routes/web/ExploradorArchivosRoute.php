<?php

Route::group([ 'middleware' => 'auth', 'prefix' => 'explorador-archivos'] ,function(){
    Route::get('/', "ExploradorArchivoController@index")->name('explorador-archivos.index');
    Route::post('/arbol/subcarpetas', 'ExploradorArchivoController@arbol')->name('arbol');
    Route::post('/subcarpetas', 'ExploradorArchivoController@subcarpetas')->name('cargar.subcarpetas');
    Route::post('/subarchivos', 'ExploradorArchivoController@subArchivos')->name('cargar.subarchivos');
    Route::get('/archivos-mostrar/{archivo}', 'ExploradorArchivoController@archivo_mostrar')->name('archivo.mostrar');
    Route::post('/archivos/upload', 'ExploradorArchivoController@almacenarArchivos')->name('enviararchivo');
    Route::post('/getarchivos', 'ExploradorArchivoController@getArchivos')->name('getArchivos');

    Route::get('/change/columnafila', 'ExploradorArchivoController@change_columna_fila')->name('configurar.columna.fila');
    Route::get('/presentacion-carpeta-item/{tipo}','ExploradorArchivoController@presentacionItem')->name('presentacion.carpeta.item');
 //Route::post('data/colecciones', 'Estadistica\EstadisticaController@load_data_collection')->name('colecciones.data');

    Route::post('/crearcarpeta', 'ExploradorArchivoController@nuevacarpeta')->name('nuevacarpeta');
    Route::post('/archivos/update_nombre', 'ExploradorArchivoController@update_nombre')->name('update_nombre');
    Route::post('/archivos/delete_elemento', 'ExploradorArchivoController@delete_elemento')->name('delete_elemento');
    Route::post('/archivos/mover_elemento', 'ExploradorArchivoController@mover_elemento')->name('mover_elemento');
    Route::post('/archivos/mover_elementos', 'ExploradorArchivoController@moverCarpetasArchivos')->name('mover_elementos_bulk');


    Route::get('/carpetas-ajax/{carpeta}/{user}', 'ExploradorArchivoController@carpetasAjax')->name('carpetas-ajax');
    Route::get('/show-carpeta-ajax/{carpeta}/{user}', 'ExploradorArchivoController@showCarpetaAjax')->name('show-carpeta-ajax');

});

/*
Route::group(['middleware' => ['web','auth', 'licencia'], 'namespace' => 'ExploradorArchivos', 'prefix' => 'explorador-archivos'], function () {
    Route::get('/{tipo}/{id}', 'GestorController@show_carpeta')->name('explorador_archivos');
    Route::get('/archivos-cliente', 'GestorController@carpetas_proyecto')->name('archivos_cliente');
    
    Route::get('/', 'GestorController@index')->name('archivos');
    Route::post('/arbol/subcarpetas', 'GestorController@arbol')->name('arbol');//aca esta el error


    Route::get('/correspondencia/{tipo}/{id}', 'GestorController@show_carpeta_correspondencia')->name('explorador_archivos_correspondencia');
});
*/