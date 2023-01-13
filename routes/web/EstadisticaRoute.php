<?php

//Route::get('/estadistica-public', 'Estadistica\EstadisticaController@index')->name('estadistica.public');
Route::group([ 'prefix' => 'estadistica'] ,function(){
    Route::get('/', 'Estadistica\EstadisticaController@index')->name('estadistica.index');
});
Route::group([ 'middleware' => 'auth', 'prefix' => 'estadistica'] ,function(){
    Route::get('/reserva-vuelo', 'Estadistica\ReservaVueloController@index')->name('reservaVuelo.index');
    Route::post('/reserva-vuelo', 'Estadistica\ReservaVueloController@store')->name('reservaVuelo.store');
    Route::get('/puerto', 'Estadistica\BarcoController@index')->name('barco.index');
    ROute::post('/puerto', 'Estadistica\BarcoController@store')->name('barco.store');

    Route::get('/colegio', 'Estadistica\ColegioController@index')->name('colegio.index');
    Route::get('/sena', 'Estadistica\SenaController@index')->name('sena.index');
    Route::get('/capuitania-puerto', 'Estadistica\CapitaniaPuertoController@index')->name('capitania.puerto.index');
    Route::get('/policia', 'Estadistica\PoliciaController@index')->name('policia.index');
    Route::get('/empresa-energia', 'Estadistica\EmpresaEnergiaController@index')->name('empresa.energia.index');
    Route::get('/empresa-aaa', 'Estadistica\EmpresaAController@index')->name('empresa.aaa.index');
    Route::get('/notaria', 'Estadistica\NotariaController@index')->name('notaria.index');
    Route::get('/hospital', 'Estadistica\HospitalController@index')->name('hospital.index');
    Route::get('/bomberos', 'Estadistica\BomberoController@index')->name('bomberos.index');
    Route::get('/ludoteca', 'Estadistica\LudotecaController@index')->name('ludoteca.index');
    Route::get('/proyecto', 'Estadistica\ProyectoController@index')->name('estadistica.proyectos');

    Route::post('store/colecciones', 'Estadistica\EstadisticaController@store_colecciones')->name('colecciones.store');
    Route::post('data/colecciones', 'Estadistica\EstadisticaController@load_data_collection')->name('colecciones.data');
});

Route::group(['prefix' => 'graficos'] ,function(){
    Route::get('/graficos/{tipo}', 'Estadistica\GraficoController@grafico')->name('graficos.mostrar');
});