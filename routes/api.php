<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/personas/predios/{numdc}', 'Cobro\PredioController@prediosApi');

//autenticacion con token
Route::group(['prefix' => 'auth', 'namespace' => 'Api'], function ($router) {
    Route::post('login', 'AuthController@login');
});

Route::group(['prefix' => 'auth', 'middleware' => 'jwt.auth', 'namespace' => 'Api'], function () {
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});

//app notificaciones 
Route::group(['prefix' => 'firebase', 'middleware' => 'jwt.auth', 'namespace' => 'Api\Firebase'], function (){
    Route::post('/notification/device_token', 'NotificationController@saveToken');// api/firebase/notification/device_token post {device_token}
});

Route::group(['prefix' => 'presupuesto', 'middleware' => 'jwt.auth', 'namespace' => 'Api\Presupuesto'], function (){
    Route::get('/cdps', 'CdpController@list'); //api/presupuesto/cdps get
    Route::post('/cdps/update-status', 'CdpController@updateStatus');//api/presupuesto/cdps/update-status post $cdps = [[id, status[0,1,2,3]]] post
});

Route::group(['prefix' => 'presupuesto', 'middleware' => 'jwt.auth'], function (){
    Route::get('cdps/pdf/{id}/{vigen}', 'Api\Presupuesto\CdpController@pdf')->name('cpd-pdf-api');
    Route::get('cdp/pdfBorrador/{id}/{vigen}', 'Api\Presupuesto\CdpController@pdfBorrador')->name('cpd-pdf-borrador-api');
});