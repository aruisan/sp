<?php
Route::group([ 'middleware' => 'auth', 'prefix' => 'nomina'] ,function(){
   // Route::get('/lista/empleado', "Nomina\NominaController@index")->name('nomina.dashboard');
    Route::get('/lista/{tipo}', "Nomina\NominaController@index")->name('nomina.index');
    Route::get('/create/{tipo}', "Nomina\NominaController@create")->name('nomina.create');
    Route::post('/', "Nomina\NominaController@store")->name('nomina.store');
    Route::get('/edit/{nomina}', "Nomina\NominaController@edit")->name('nomina.edit');
    Route::post('/update/{nomina}', "Nomina\NominaController@update")->name('nomina.update');
    Route::post('/update/empleado/{nomina}', "Nomina\NominaController@update_empleado")->name('nomina.update.empleado');
    Route::get('/show/{nomina}', "Nomina\NominaController@show")->name('nomina.show');
    Route::get('/empleados-cuentas/{nomina}', "Nomina\NominaController@cuentas_bancarias_usuarios")->name('nomina.empleados-cuentas');



    Route::get('/pdf/{nomina}', "Nomina\NominaController@pdf_nomina")->name('nomina.pdf');
    Route::get('/pdf-desprendibles/{nomina}', "Nomina\NominaController@pdf_desprendibles")->name('nomina.pdf-desprendibles');
    Route::get('/pdf-contabilidad-presupuestal', "Nomina\NominaController@pdf_contabilidad_presupuestal")->name('nomina.pdf-contabilidad');

    //empleqados
    Route::get('/empleados', "Nomina\EmpleadoController@index")->name('nomina.empleados.index');
    Route::get('/empleados/create', "Nomina\EmpleadoController@create")->name('nomina.empleados.create');
    Route::post('/empleados/create', "Nomina\EmpleadoController@store")->name('nomina.empleados.store');
    Route::get('/empleados/edit/{employee}', "Nomina\EmpleadoController@edit")->name('nomina.empleados.edit');
    Route::post('/empleados/edit/{employee}', "Nomina\EmpleadoController@update")->name('nomina.empleados.update');
    Route::get('/empleados/status/{employee}', "Nomina\EmpleadoController@status")->name('nomina.empleados.status');

     //pensionados
     Route::get('/pensionados', "Nomina\PensionadoController@index")->name('nomina.pensionados.index');
     Route::get('/pensionados/create', "Nomina\PensionadoController@create")->name('nomina.pensionados.create');
     Route::post('/pensionados/create', "Nomina\PensionadoController@store")->name('nomina.pensionados.store');
     Route::get('/pensionados/edit/{employee}', "Nomina\PensionadoController@edit")->name('nomina.pensionados.edit');
     Route::post('/pensionados/edit/{employee}', "Nomina\PensionadoController@update")->name('nomina.pensionados.update');


    Route::get('/pagos', "Nomina\PagoController@index")->name('nomina.pagos.index');
    
});

Route::group([ 'middleware' => 'auth', 'prefix' => 'nomina-vacaciones'] ,function(){
    Route::get('/', "Nomina\VacacionesController@index")->name('nomina-vacaciones.index');
    Route::get('/crear', "Nomina\VacacionesController@create")->name('nomina-vacaciones.create');
    Route::post('/', "Nomina\VacacionesController@store")->name('nomina-vacaciones.store');
    Route::get('/edit/{nomina}', "Nomina\VacacionesController@edit")->name('nomina-vacaciones.edit');
    Route::post('/update/{nomina}', "Nomina\VacacionesController@update")->name('nomina-vacaciones.update');
    Route::get('/{nomina}', "Nomina\VacacionesController@show")->name('nomina-vacaciones.show');
    Route::get('/pdf/{nomina}', "Nomina\VacacionesController@pdf_nomina")->name('nomina-vacaciones.pdf');
    Route::get('/pdf-desprendibles/{nomina}', "Nomina\VacacionesController@pdf_desprendibles")->name('nomina-vacaciones.pdf-desprendibles');
});

Route::group([ 'middleware' => 'auth', 'prefix' => 'nomina-horas'] ,function(){
    Route::get('/', "Nomina\HorasController@index")->name('nomina-horas.index');
    Route::get('/crear', "Nomina\HorasController@create")->name('nomina-horas.create');
    Route::post('/', "Nomina\HorasController@store")->name('nomina-horas.store');
    Route::get('/edit/{nomina}', "Nomina\HorasController@edit")->name('nomina-horas.edit');
    Route::post('/update/{nomina}', "Nomina\HorasController@update")->name('nomina-horas.update');
    Route::get('/{nomina}', "Nomina\HorasController@show")->name('nomina-horas.show');
    Route::get('/pdf/{nomina}', "Nomina\HorasController@pdf_nomina")->name('nomina-horas.pdf');
    /*
    Route::get('/pdf-desprendibles/{nomina}', "Nomina\VacacionesController@pdf_desprendibles")->name('nomina-vacaciones.pdf-desprendibles');
    */
});


Route::group([ 'middleware' => 'auth', 'prefix' => 'nomina-descuentos'] ,function(){
    Route::get('/', "Nomina\DescuentosController@index")->name('nomina-descuentos.index');
    Route::get('/crear', "Nomina\DescuentosController@create")->name('nomina-descuentos.create');
    Route::post('/', "Nomina\DescuentosController@store")->name('nomina-descuentos.store');
    Route::get('/edit/{nomina}', "Nomina\DescuentosController@edit")->name('nomina-descuentos.edit');
    Route::post('/update/{nomina}', "Nomina\DescuentosController@update")->name('nomina-descuentos.update');
    Route::get('/{nomina}', "Nomina\DescuentosController@show")->name('nomina-descuentos.show');
    Route::get('/pdf/{nomina}', "Nomina\DescuentosController@pdf_nomina")->name('nomina-descuentos.pdf');
});
