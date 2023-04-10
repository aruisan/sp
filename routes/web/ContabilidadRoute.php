<?php

Route::group([ 'middleware' => 'auth', 'prefix' => 'administrativo'] ,function(){
    Route::get('/pac-informe/{tipo}', "\App\Http\Controllers\Administrativo\Tesoreria\PacController@informe_temporal")->name('pac.informe');
});