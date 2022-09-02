<?php


Route::group([ 'middleware' => 'auth', 'prefix' => 'estadistica'] ,function(){
    Route::get('/', 'Estadistica\EstadisticaController@index')->name('estadistica.index');
    Route::get('/reserva-vuelo', 'Estadistica\ReservaVueloController@index')->name('reservaVuelo.index');
    Route::post('/reserva-vuelo', 'Estadistica\ReservaVueloController@store')->name('reservaVuelo.store');

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
});