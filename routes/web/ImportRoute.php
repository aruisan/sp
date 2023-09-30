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

    Route::get('/reintegro', "ImportEstadisticaPresupuestoController@create_reintegro");
    Route::post('/reintegro', "ImportEstadisticaPresupuestoController@import_reintegro")->name('import.reintegro');

    Route::get('/puc_corriente', "ImportEstadisticaPresupuestoController@create_puc_corriente");
    Route::post('/puc_corriente', "ImportEstadisticaPresupuestoController@import_puc_corriente")->name('import.puc_corriente');

    Route::get('/empleados/sueldo', "ImportEstadisticaPresupuestoController@update_empleados_sueldo");
    Route::post('/empleados/sueldo', "ImportEstadisticaPresupuestoController@import_empleados_sueldo")->name('import.empleado.sueldo');

    Route::get('/empleados/retroactivo', "ImportEstadisticaPresupuestoController@update_empleados_retroactivo");
    Route::post('/empleados/retroactivo', "ImportEstadisticaPresupuestoController@import_empleados_retroactivo")->name('import.empleado.retroactivo');
});