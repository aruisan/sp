<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => ['web','auth']], function () {

    Route::get('bpin', 'BPinController@index')->name('bpin.index');
    Route::get('bpin/create', 'BPinController@create')->name('bpin.create');
    Route::post('bpin', 'BPinController@store')->name('bpin.store');
    Route::get('bpin/{bpin}', 'BPinController@show')->name('bpin.show');
});