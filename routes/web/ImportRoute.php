<?php

Route::group([ 'middleware' => 'auth', 'prefix' => 'import'] ,function(){
    Route::get('/estadistica', "ImportEstadisticaPresupuestoController@create");
    Route::post('/estadistica', "ImportEstadisticaPresupuestoController@import")->name('import.presupuesto-estadistica');

    Route::get('/terceros', "ImportEstadisticaPresupuestoController@create_terceros");
    Route::post('/terceros', "ImportEstadisticaPresupuestoController@import_terceros")->name('import.tercero-estadistica');

    Route::get('/empleados', "ImportEstadisticaPresupuestoController@create_empleados");
    Route::post('/empleados', "ImportEstadisticaPresupuestoController@import_empleados")->name('import.empleado');

    Route::get('/empleados-cuentas', "ImportEstadisticaPresupuestoController@create_empleados_cuentas");
    Route::post('/empleados-cuentas', "ImportEstadisticaPresupuestoController@import_empleados_cuentas")->name('import.empleado-cuentas');

    Route::get('/bancos-saldos-iniciales', "ImportEstadisticaPresupuestoController@create_bancos_saldos_iniciales");
    Route::post('/bancos-saldos-iniciales', "ImportEstadisticaPresupuestoController@import_bancos_saldos_iniciales")->name('import.bancos_saldos_iniciales');


    Route::get('/comprobante-ingreso-temporal', "ImportEstadisticaPresupuestoController@create_comprobantes_old");
    Route::post('/comprobante-ingreso-temporal', "ImportEstadisticaPresupuestoController@import_comprobantes_old")->name('import.comprobantes_old');

    Route::get('/pac', "ImportEstadisticaPresupuestoController@create_pac");
    Route::post('/pac', "ImportEstadisticaPresupuestoController@import_pac")->name('import.pac');
});