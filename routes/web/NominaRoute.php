<?php
Route::group([ 'middleware' => 'auth', 'prefix' => 'nomina'] ,function(){
    Route::get('/lista/{tipo}', "Nomina\NominaController@index")->name('nomina.index');
    Route::get('/create/{tipo}', "Nomina\NominaController@create")->name('nomina.create');
    Route::post('/', "Nomina\NominaController@store")->name('nomina.store');
    Route::get('/edit/{nomina}', "Nomina\NominaController@edit")->name('nomina.edit');
    Route::get('/show/{nomina}', "Nomina\NominaController@show")->name('nomina.show');

    //empleqados
    Route::get('/empleados', "Nomina\EmpleadoController@index")->name('nomina.empleados.index');
    Route::get('/empleados/create', "Nomina\EmpleadoController@create")->name('nomina.empleados.create');
    Route::post('/empleados/create', "Nomina\EmpleadoController@store")->name('nomina.empleados.store');
    Route::get('/empleados/edit/{employee}', "Nomina\EmpleadoController@edit")->name('nomina.empleados.edit');
    Route::post('/empleados/edit/{employee}', "Nomina\EmpleadoController@update")->name('nomina.empleados.update');

     //pensionados
     Route::get('/pensionados', "Nomina\PensionadoController@index")->name('nomina.pensionados.index');
     Route::get('/pensionados/create', "Nomina\PensionadoController@create")->name('nomina.pensionados.create');
     Route::post('/pensionados/create', "Nomina\PensionadoController@store")->name('nomina.pensionados.store');
     Route::get('/pensionados/edit/{employee}', "Nomina\PensionadoController@edit")->name('nomina.pensionados.edit');
     Route::post('/pensionados/edit/{employee}', "Nomina\PensionadoController@update")->name('nomina.pensionados.update');


    Route::get('/pagos', "Nomina\PagoController@index")->name('nomina.pagos.index');
});
