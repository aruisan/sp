<?php


Route::group([ 'middleware' => 'auth', 'prefix' => 'estadistica'] ,function(){
    Route::get('/', 'Estadistica\EstadisticaController@index')->name('estadistica.index');
});
